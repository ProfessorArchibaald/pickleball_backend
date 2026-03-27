<?php

namespace Tests\Feature\Api;

use App\Data\Matches\StoreMatchData;
use App\Models\Dictionary\Game\GameFormat;
use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchPoint;
use App\Models\User;
use App\Services\Matches\CreateMatchService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(?User $user = null): array
    {
        $user ??= User::factory()->create();

        $token = $user->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];
    }

    private function linkGameFormatToGameType(GameType $gameType, int $gameFormatId): GameFormat
    {
        GameFormatType::query()->updateOrCreate(
            ['game_type_id' => $gameType->id],
            ['game_format_id' => $gameFormatId],
        );

        return GameFormat::query()->findOrFail($gameFormatId);
    }

    /**
     * @return array<int, array{user_id:int}>
     */
    private function playerPayload(int ...$userIds): array
    {
        return collect($userIds)
            ->map(fn (int $userId): array => ['user_id' => $userId])
            ->all();
    }

    public function test_guest_cannot_create_a_match(): void
    {
        $response = $this->postJson('/api/matches');

        $response->assertUnauthorized();
    }

    public function test_match_can_be_created(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $gameFormat = $this->linkGameFormatToGameType($gameType, 2);
        $creator = User::factory()->create();
        $players = User::factory()->count(3)->create();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'players' => $this->playerPayload(...$players->pluck('id')->all()),
        ], $this->authHeaders($creator));

        $response
            ->assertCreated()
            ->assertJsonPath('data.game_type_id', $gameType->id)
            ->assertJsonPath('data.game_type.id', $gameType->id)
            ->assertJsonPath('data.game_type.name', $gameType->name)
            ->assertJsonPath('data.game_format_id', $gameFormat->id)
            ->assertJsonPath('data.game_format.id', $gameFormat->id)
            ->assertJsonPath('data.game_format.name', $gameFormat->name)
            ->assertJsonPath('data.game_format.number_of_players', $gameFormat->number_of_players)
            ->assertJsonPath('data.finished_at', null)
            ->assertJsonPath('data.duration', null)
            ->assertJsonPath('data.is_finished', false);

        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseHas('matches', [
            'id' => $response->json('data.id'),
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'finished_at' => null,
            'duration' => null,
        ]);

        $matchId = $response->json('data.id');

        $this->assertDatabaseCount('match_players', 4);
        $this->assertDatabaseHas('match_players', [
            'match_id' => $matchId,
            'user_id' => $creator->id,
            'is_creator' => true,
        ]);

        foreach ($players as $player) {
            $this->assertDatabaseHas('match_players', [
                'match_id' => $matchId,
                'user_id' => $player->id,
                'is_creator' => false,
            ]);
        }

        $this->assertDatabaseCount('match_points', 1);

        $initialMatchPoint = MatchPoint::query()->where('match_id', $matchId)->first();

        $this->assertNotNull($initialMatchPoint);
        $this->assertSame(0, $initialMatchPoint->team1_score);
        $this->assertSame(0, $initialMatchPoint->team2_score);
        $this->assertNull($initialMatchPoint->win_point_player_id);
        $this->assertContains(
            $initialMatchPoint->serve_player_id,
            MatchPlayer::query()->where('match_id', $matchId)->pluck('id')->all(),
        );

        $assignedTeams = MatchPlayer::query()
            ->where('match_id', $matchId)
            ->orderBy('id')
            ->pluck('team')
            ->all();

        $this->assertTrue(
            collect($assignedTeams)->every(fn (int $team): bool => in_array($team, [1, 2], true)),
        );
        $this->assertSame([2, 2], collect($assignedTeams)->countBy()->sortKeys()->values()->all());
    }

    public function test_match_creation_passes_a_data_object_to_the_service(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $gameFormat = $this->linkGameFormatToGameType($gameType, 1);
        $creator = User::factory()->create();
        $player = User::factory()->create();
        $fakeMatch = GameMatch::factory()->make([
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'finished_at' => null,
            'created_at' => now(),
        ])
            ->setRelation('gameType', $gameType)
            ->setRelation('gameFormat', $gameFormat);

        $spy = (object) ['data' => null];

        $this->app->bind(CreateMatchService::class, function () use ($fakeMatch, $spy): CreateMatchService {
            return new class($fakeMatch, $spy) extends CreateMatchService
            {
                public function __construct(
                    private readonly GameMatch $match,
                    private readonly object $spy,
                ) {}

                public function create(StoreMatchData $data): GameMatch
                {
                    $this->spy->data = $data;

                    return $this->match;
                }
            };
        });

        $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'players' => $this->playerPayload($player->id),
        ], $this->authHeaders($creator))
            ->assertCreated()
            ->assertJsonPath('data.id', $fakeMatch->id)
            ->assertJsonPath('data.game_type_id', $gameType->id)
            ->assertJsonPath('data.game_format_id', $gameFormat->id);

        $receivedData = $spy->data;
        $this->assertInstanceOf(StoreMatchData::class, $receivedData);
        $this->assertSame($gameType->id, $receivedData->gameTypeId);
        $this->assertSame($gameFormat->id, $receivedData->gameFormatId);
        $this->assertSame([$player->id], $receivedData->playerUserIds);
        $this->assertSame($creator->id, $receivedData->creatorUserId);
    }

    public function test_authenticated_user_can_get_actual_match_point(): void
    {
        $match = GameMatch::factory()->create();
        $servePlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
        ]);
        $winPointPlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
        ]);

        MatchPoint::factory()->create([
            'match_id' => $match->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 2,
            'team2_score' => 1,
            'win_point_player_id' => $winPointPlayer->id,
        ]);

        $latestMatchPoint = MatchPoint::factory()->create([
            'match_id' => $match->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 3,
            'team2_score' => 1,
            'win_point_player_id' => $servePlayer->id,
        ]);

        $response = $this->getJson("/api/matches/{$match->id}/actual-point", $this->authHeaders());

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $latestMatchPoint->id)
            ->assertJsonPath('data.match_id', $match->id)
            ->assertJsonPath('data.serve_player_id', $servePlayer->id)
            ->assertJsonPath('data.team1_score', 3)
            ->assertJsonPath('data.team2_score', 1)
            ->assertJsonPath('data.win_point_player_id', $servePlayer->id);
    }

    public function test_guest_cannot_get_actual_match_point(): void
    {
        $match = GameMatch::factory()->create();

        $response = $this->getJson("/api/matches/{$match->id}/actual-point");

        $response->assertUnauthorized();
    }

    public function test_match_point_update_creates_next_point_and_increments_score_when_server_wins_point(): void
    {
        $match = GameMatch::factory()->create();
        $servePlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
            'team' => 1,
        ]);
        $matchPoint = MatchPoint::factory()->create([
            'match_id' => $match->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 4,
            'team2_score' => 3,
            'win_point_player_id' => null,
        ]);

        $response = $this->patchJson("/api/match-points/{$matchPoint->id}", [
            'win_point_player_id' => $servePlayer->id,
            'team1_score' => 99,
            'team2_score' => 99,
        ], $this->authHeaders());

        $latestMatchPoint = MatchPoint::query()
            ->where('match_id', $match->id)
            ->latest('id')
            ->firstOrFail();

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $latestMatchPoint->id)
            ->assertJsonPath('data.win_point_player_id', null)
            ->assertJsonPath('data.team1_score', 5)
            ->assertJsonPath('data.team2_score', 3)
            ->assertJsonPath('data.serve_player_id', $servePlayer->id);

        $this->assertNotSame($matchPoint->id, $latestMatchPoint->id);
        $this->assertSame(2, MatchPoint::query()->where('match_id', $match->id)->count());

        $this->assertDatabaseHas('match_points', [
            'id' => $matchPoint->id,
            'win_point_player_id' => $servePlayer->id,
            'team1_score' => 5,
            'team2_score' => 3,
        ]);

        $this->assertDatabaseHas('match_points', [
            'id' => $latestMatchPoint->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 5,
            'team2_score' => 3,
            'win_point_player_id' => null,
        ]);
    }

    public function test_match_point_update_creates_next_point_with_new_server_when_receiver_wins_point(): void
    {
        $match = GameMatch::factory()->create();
        $servePlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
            'team' => 1,
        ]);
        $winnerPlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
            'team' => 2,
        ]);
        $matchPoint = MatchPoint::factory()->create([
            'match_id' => $match->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 6,
            'team2_score' => 4,
            'win_point_player_id' => null,
        ]);

        $response = $this->patchJson("/api/match-points/{$matchPoint->id}", [
            'win_point_player_id' => $winnerPlayer->id,
        ], $this->authHeaders());

        $latestMatchPoint = MatchPoint::query()
            ->where('match_id', $match->id)
            ->latest('id')
            ->firstOrFail();

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $latestMatchPoint->id)
            ->assertJsonPath('data.serve_player_id', $winnerPlayer->id)
            ->assertJsonPath('data.team1_score', 6)
            ->assertJsonPath('data.team2_score', 4)
            ->assertJsonPath('data.win_point_player_id', null);

        $this->assertNotSame($matchPoint->id, $latestMatchPoint->id);
        $this->assertSame(2, MatchPoint::query()->where('match_id', $match->id)->count());

        $this->assertDatabaseHas('match_points', [
            'id' => $matchPoint->id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 6,
            'team2_score' => 4,
            'win_point_player_id' => $winnerPlayer->id,
        ]);

        $this->assertDatabaseHas('match_points', [
            'id' => $latestMatchPoint->id,
            'serve_player_id' => $winnerPlayer->id,
            'team1_score' => 6,
            'team2_score' => 4,
            'win_point_player_id' => null,
        ]);
    }

    public function test_match_point_update_requires_winner_player_from_the_same_match(): void
    {
        $match = GameMatch::factory()->create();
        $servePlayer = MatchPlayer::factory()->create([
            'match_id' => $match->id,
        ]);
        $matchPoint = MatchPoint::factory()->create([
            'match_id' => $match->id,
            'serve_player_id' => $servePlayer->id,
            'win_point_player_id' => null,
        ]);
        $otherMatchPlayer = MatchPlayer::factory()->create();

        $response = $this->patchJson("/api/match-points/{$matchPoint->id}", [
            'win_point_player_id' => $otherMatchPlayer->id,
        ], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['win_point_player_id']);
    }

    public function test_guest_cannot_update_match_point(): void
    {
        $matchPoint = MatchPoint::factory()->create();

        $response = $this->patchJson("/api/match-points/{$matchPoint->id}", [
            'win_point_player_id' => MatchPlayer::factory()->create([
                'match_id' => $matchPoint->match_id,
            ])->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_match_creation_requires_a_valid_game_type_id(): void
    {
        $response = $this->postJson('/api/matches', [], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['game_type_id']);
    }

    public function test_match_creation_requires_a_valid_game_format_id(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $player = User::factory()->create();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'players' => $this->playerPayload($player->id),
        ], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['game_format_id']);
    }

    public function test_match_creation_requires_a_game_format_available_for_the_selected_game_type(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $player = User::factory()->create();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'game_format_id' => 3,
            'players' => $this->playerPayload($player->id),
        ], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['game_format_id'])
            ->assertJsonPath(
                'errors.game_format_id.0',
                'The selected game format is invalid for the provided game type.',
            );
    }

    public function test_match_creation_requires_players_count_to_match_the_selected_game_format(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $gameFormat = $this->linkGameFormatToGameType($gameType, 2);
        $players = User::factory()->count(2)->create();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'players' => $this->playerPayload(...$players->pluck('id')->all()),
        ], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['players'])
            ->assertJsonPath(
                'errors.players.0',
                'The players field must contain exactly 3 players for the selected game format.',
            );
    }

    public function test_match_creation_rejects_the_authenticated_user_in_players_payload(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $gameFormat = $this->linkGameFormatToGameType($gameType, 1);
        $creator = User::factory()->create();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormat->id,
            'players' => $this->playerPayload($creator->id),
        ], $this->authHeaders($creator));

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['players.0.user_id'])
            ->assertJsonFragment([
                'The authenticated user is added automatically and must not be included in players.',
            ]);
    }

    public function test_match_can_be_finished(): void
    {
        $createdAt = CarbonImmutable::parse('2026-03-24 10:00:00');
        $finishedAt = CarbonImmutable::parse('2026-03-24 10:07:45');
        $expectedDuration = (int) $createdAt->diffInSeconds($finishedAt);

        $match = GameMatch::factory()->create([
            'created_at' => $createdAt,
        ]);

        CarbonImmutable::setTestNow($finishedAt);

        $response = $this->patchJson("/api/matches/{$match->id}/finish", [], $this->authHeaders());

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $match->id)
            ->assertJsonPath('data.game_type_id', $match->game_type_id)
            ->assertJsonPath('data.game_type.id', $match->gameType->id)
            ->assertJsonPath('data.game_type.name', $match->gameType->name)
            ->assertJsonPath('data.game_format_id', $match->game_format_id)
            ->assertJsonPath('data.game_format.id', $match->gameFormat->id)
            ->assertJsonPath('data.game_format.name', $match->gameFormat->name)
            ->assertJsonPath('data.game_format.number_of_players', $match->gameFormat->number_of_players)
            ->assertJsonPath('data.finished_at', $finishedAt->toISOString())
            ->assertJsonPath('data.duration', $expectedDuration)
            ->assertJsonPath('data.is_finished', true);

        $freshMatch = $match->fresh();
        $this->assertNotNull($freshMatch->finished_at);
        $this->assertSame($expectedDuration, $freshMatch->duration);

        CarbonImmutable::setTestNow();
    }

    public function test_finishing_an_already_finished_match_keeps_the_original_finished_at(): void
    {
        $finishedAt = CarbonImmutable::parse('2026-03-24 10:00:00');

        $match = GameMatch::factory()->create([
            'finished_at' => $finishedAt,
            'duration' => 300,
        ]);

        $response = $this->patchJson("/api/matches/{$match->id}/finish", [], $this->authHeaders());

        $response
            ->assertOk()
            ->assertJsonPath('data.is_finished', true)
            ->assertJsonPath('data.finished_at', $finishedAt->toISOString())
            ->assertJsonPath('data.duration', 300);

        $freshMatch = $match->fresh();
        $freshFinishedAt = $freshMatch?->finished_at;
        $this->assertNotNull($freshFinishedAt);
        $this->assertTrue($freshFinishedAt->equalTo($finishedAt));
        $this->assertSame(300, $freshMatch?->duration);
    }

    public function test_guest_cannot_finish_a_match(): void
    {
        $match = GameMatch::factory()->create();

        $response = $this->patchJson("/api/matches/{$match->id}/finish");

        $response->assertUnauthorized();
    }
}
