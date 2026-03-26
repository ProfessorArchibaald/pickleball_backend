<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\GameMatches\GameMatchResource;
use App\Filament\Resources\GameMatches\Pages\ListGameMatches;
use App\Models\GameMatch;
use App\Models\User;
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
        $response->assertSee((string) $match->game_type_id);
    }

    public function test_matches_list_has_no_create_edit_or_delete_actions(): void
    {
        $adminUser = User::factory()->admin()->create();
        $match = GameMatch::factory()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameMatches::class)
            ->assertActionDoesNotExist('create')
            ->assertTableActionDoesNotExist('edit', null, $match)
            ->assertTableActionDoesNotExist('delete', null, $match)
            ->assertTableBulkActionDoesNotExist('delete');
    }

    public function test_matches_list_contains_database_columns(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameMatches::class)
            ->assertTableColumnExists('id')
            ->assertTableColumnExists('game_type_id')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('finished_at');
    }
}
