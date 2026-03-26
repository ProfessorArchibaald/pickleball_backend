<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Filament\Resources\GameMatches\Pages\EditGameMatch;
use App\Filament\Resources\GameMatches\Pages\ListGameMatches;
use App\Models\GameMatch;
use App\Models\User;
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
    }

    public function test_matches_list_has_no_create_or_delete_actions(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameMatches::class)
            ->assertActionDoesNotExist('create')
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
        $this->assertNotNull($freshMatch?->finished_at);
        $this->assertSame($expectedDuration, $freshMatch?->duration);

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
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('finished_at')
            ->assertTableColumnExists('duration');
    }
}
