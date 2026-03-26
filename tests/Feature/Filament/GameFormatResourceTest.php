<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Dictionary\GameFormats\GameFormatResource;
use App\Filament\Resources\Dictionary\GameFormats\Pages\ListGameFormats;
use App\Models\Dictionary\Game\GameFormat;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GameFormatResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_game_formats_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(GameFormatResource::getUrl());

        $response->assertOk();
        $response->assertSee('Game Formats');
        $response->assertSee('1x1');
        $response->assertSee('2x2');
        $response->assertSee('1x2');
    }

    public function test_create_action_is_not_available_for_game_formats(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameFormats::class)
            ->assertActionDoesNotExist('create');
    }

    public function test_edit_and_delete_actions_are_not_available_for_game_formats(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameFormat = GameFormat::query()->findOrFail(1);

        Livewire::actingAs($adminUser)
            ->test(ListGameFormats::class)
            ->assertTableActionDoesNotExist('edit', null, $gameFormat)
            ->assertTableActionDoesNotExist('delete', null, $gameFormat)
            ->assertTableBulkActionDoesNotExist('delete');
    }

    public function test_game_formats_list_contains_dictionary_columns(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameFormats::class)
            ->assertTableColumnExists('id')
            ->assertTableColumnExists('name');
    }
}
