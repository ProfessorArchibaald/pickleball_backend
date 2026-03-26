<?php

namespace Tests\Feature\Api;

use App\Data\Matches\StoreMatchData;
use App\Models\Dictionary\Game\GameType;
use App\Models\GameMatch;
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

    public function test_guest_cannot_create_a_match(): void
    {
        $response = $this->postJson('/api/matches');

        $response->assertUnauthorized();
    }

    public function test_match_can_be_created(): void
    {
        $gameType = GameType::query()->firstOrFail();

        $response = $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
        ], $this->authHeaders());

        $response
            ->assertCreated()
            ->assertJsonPath('data.game_type_id', $gameType->id)
            ->assertJsonPath('data.game_type.id', $gameType->id)
            ->assertJsonPath('data.game_type.name', $gameType->name)
            ->assertJsonPath('data.finished_at', null)
            ->assertJsonPath('data.duration', null)
            ->assertJsonPath('data.is_finished', false);

        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseHas('matches', [
            'id' => $response->json('data.id'),
            'game_type_id' => $gameType->id,
            'finished_at' => null,
            'duration' => null,
        ]);
    }

    public function test_match_creation_passes_a_data_object_to_the_service(): void
    {
        $gameType = GameType::query()->firstOrFail();
        $fakeMatch = GameMatch::factory()->make([
            'game_type_id' => $gameType->id,
            'finished_at' => null,
            'created_at' => now(),
        ])->setRelation('gameType', $gameType);

        $spy = (object) ['data' => null];

        $this->app->bind(CreateMatchService::class, function () use ($fakeMatch, $spy): CreateMatchService {
            return new class ($fakeMatch, $spy) extends CreateMatchService {
                public function __construct(
                    private readonly GameMatch $match,
                    private readonly object $spy,
                ) {
                }

                public function create(StoreMatchData $data): GameMatch
                {
                    $this->spy->data = $data;

                    return $this->match;
                }
            };
        });

        $this->postJson('/api/matches', [
            'game_type_id' => $gameType->id,
        ], $this->authHeaders())
            ->assertCreated()
            ->assertJsonPath('data.id', $fakeMatch->id)
            ->assertJsonPath('data.game_type_id', $gameType->id);

        $receivedData = $spy->data;
        $this->assertInstanceOf(StoreMatchData::class, $receivedData);
        $this->assertSame($gameType->id, $receivedData->gameTypeId);
    }

    public function test_match_creation_requires_a_valid_game_type_id(): void
    {
        $response = $this->postJson('/api/matches', [], $this->authHeaders());

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['game_type_id']);
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
