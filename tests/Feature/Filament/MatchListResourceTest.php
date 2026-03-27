<?php

namespace Tests\Feature\Filament;

use App\Data\Matches\StoreMatchData;
use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Filament\Resources\GameMatches\Pages\CreateGameMatch;
use App\Filament\Resources\GameMatches\Pages\EditGameMatch;
use App\Filament\Resources\GameMatches\Pages\ListGameMatches;
use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\User;
use App\Services\Matches\CreateMatchService;
use Carbon\CarbonImmutable;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MatchListResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_matches_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(GameMatchResource::getUrl());

        $response->assertOk();
        $response->assertSee('Matches');
        $response->assertSee((string) $match->id);
        $response->assertSee($match->gameType->name);
        $response->assertSee($match->gameFormat->name);
    }

    public function test_admin_can_open_match_create_page(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(GameMatchResource::getUrl('create'));

        $response->assertOk();
        $response->assertSee('Create');
        $response->assertSee('Game type');
        $response->assertSee('Game format');
        $response->assertSee('Players');
    }

    public function test_admin_can_open_match_edit_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create()->load('gameType');

        $response = $this
            ->actingAs($adminUser)
            ->get(GameMatchResource::getUrl('edit', ['record' => $match]));

        $response->assertOk();
        $response->assertSee('Edit');
        $response->assertSee($match->gameType->name);
        $response->assertSee($match->gameFormat->name);
    }

    public function test_admin_can_create_match_from_filament(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();
        $gameFormatId = 2;
        $players = User::factory()->count(3)->create();

        GameFormatType::query()->updateOrCreate(
            ['game_type_id' => $gameType->id],
            ['game_format_id' => $gameFormatId],
        );

        Livewire::actingAs($adminUser)
            ->test(CreateGameMatch::class)
            ->fillForm([
                'game_type_id' => $gameType->id,
                'game_format_id' => $gameFormatId,
                'player_user_ids' => $players->pluck('id')->all(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $match = GameMatch::query()->latest('id')->firstOrFail();

        $this->assertDatabaseHas(GameMatch::class, [
            'id' => $match->id,
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormatId,
            'finished_at' => null,
            'duration' => null,
        ]);

        $this->assertDatabaseCount('match_players', 4);
        $this->assertDatabaseHas(MatchPlayer::class, [
            'match_id' => $match->id,
            'user_id' => $adminUser->id,
            'is_creator' => true,
        ]);

        foreach ($players as $player) {
            $this->assertDatabaseHas(MatchPlayer::class, [
                'match_id' => $match->id,
                'user_id' => $player->id,
                'is_creator' => false,
            ]);
        }
    }

    public function test_filament_match_creation_uses_create_match_service(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();
        $gameFormatId = 1;
        $player = User::factory()->create();
        $fakeMatch = GameMatch::factory()->create([
            'game_type_id' => $gameType->id,
            'game_format_id' => $gameFormatId,
        ]);

        GameFormatType::query()->updateOrCreate(
            ['game_type_id' => $gameType->id],
            ['game_format_id' => $gameFormatId],
        );

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

        Livewire::actingAs($adminUser)
            ->test(CreateGameMatch::class)
            ->fillForm([
                'game_type_id' => $gameType->id,
                'game_format_id' => $gameFormatId,
                'player_user_ids' => [$player->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $receivedData = $spy->data;
        $this->assertInstanceOf(StoreMatchData::class, $receivedData);
        $this->assertSame($gameType->id, $receivedData->gameTypeId);
        $this->assertSame($gameFormatId, $receivedData->gameFormatId);
        $this->assertSame([$player->id], $receivedData->playerUserIds);
        $this->assertSame($adminUser->id, $receivedData->creatorUserId);
    }

    public function test_matches_list_has_create_action_but_no_delete_actions(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameMatches::class)
            ->assertActionExists('create')
            ->assertTableActionDoesNotExist('delete', null, $match)
            ->assertTableBulkActionDoesNotExist('delete');
    }

    public function test_admin_can_finish_match_from_edit_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $createdAt = CarbonImmutable::parse('2026-03-24 10:00:00');
        $finishedAt = CarbonImmutable::parse('2026-03-24 10:07:45');
        $expectedDuration = (int) $createdAt->diffInSeconds($finishedAt);
        $match = GameMatch::factory()->create([
            'created_at' => $createdAt,
            'finished_at' => null,
        ]);

        CarbonImmutable::setTestNow($finishedAt);

        Livewire::actingAs($adminUser)
            ->test(EditGameMatch::class, ['record' => $match->getRouteKey()])
            ->assertActionExists('finishMatch')
            ->callAction('finishMatch');

        $freshMatch = $match->fresh();
        $this->assertNotNull($freshMatch->finished_at);
        $this->assertSame($expectedDuration, $freshMatch->duration);

        CarbonImmutable::setTestNow();
    }

    public function test_finish_match_action_is_hidden_for_finished_match(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create([
            'finished_at' => CarbonImmutable::parse('2026-03-24 10:10:00'),
            'duration' => 600,
        ]);

        Livewire::actingAs($adminUser)
            ->test(EditGameMatch::class, ['record' => $match->getRouteKey()])
            ->assertActionHidden('finishMatch');
    }

    public function test_matches_list_contains_database_columns(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameMatches::class)
            ->assertTableColumnExists('id')
            ->assertTableColumnExists('gameType.name')
            ->assertTableColumnExists('gameFormat.name')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('finished_at')
            ->assertTableColumnExists('duration');
    }
}
