<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Dictionary\GameTypes\GameTypeResource;
use App\Filament\Resources\Dictionary\GameTypes\Pages\EditGameType;
use App\Filament\Resources\Dictionary\GameTypes\Pages\ListGameTypes;
use App\Models\Dictionary\GameType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GameTypeResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_game_types_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();

        $response = $this
            ->actingAs($adminUser)
            ->get(GameTypeResource::getUrl());

        $response->assertOk();
        $response->assertSee('Game Types');
        $response->assertSee($gameType->name);
    }

    public function test_admin_can_edit_game_type_name(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();

        Livewire::actingAs($adminUser)
            ->test(EditGameType::class, ['record' => $gameType->getRouteKey()])
            ->fillForm([
                'name' => 'Pickleball',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(GameType::class, [
            'id' => $gameType->id,
            'name' => 'Pickleball',
        ]);
    }

    public function test_create_action_is_not_available_for_game_types(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameTypes::class)
            ->assertActionDoesNotExist('create');
    }

    public function test_delete_action_is_not_available_for_game_types(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();

        Livewire::actingAs($adminUser)
            ->test(EditGameType::class, ['record' => $gameType->getRouteKey()])
            ->assertActionDoesNotExist('delete');
    }
}
