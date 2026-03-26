<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Dictionary\GameFormatTypes\GameFormatTypeResource;
use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\CreateGameFormatType;
use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\EditGameFormatType;
use App\Filament\Resources\Dictionary\GameFormatTypes\Pages\ListGameFormatTypes;
use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GameFormatTypeResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_game_format_types_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(GameFormatTypeResource::getUrl());

        $response->assertOk();
        $response->assertSee('Game Format Types');
    }

    public function test_admin_can_create_game_format_type(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();

        Livewire::actingAs($adminUser)
            ->test(CreateGameFormatType::class)
            ->fillForm([
                'game_type_id' => $gameType->id,
                'game_format_id' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(GameFormatType::class, [
            'game_type_id' => $gameType->id,
            'game_format_id' => 1,
        ]);
    }

    public function test_admin_can_edit_game_format_type(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();
        $gameFormatType = GameFormatType::query()->create([
            'game_type_id' => $gameType->id,
            'game_format_id' => 1,
        ]);

        Livewire::actingAs($adminUser)
            ->test(EditGameFormatType::class, ['record' => $gameFormatType->getRouteKey()])
            ->fillForm([
                'game_type_id' => $gameType->id,
                'game_format_id' => 2,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(GameFormatType::class, [
            'id' => $gameFormatType->id,
            'game_type_id' => $gameType->id,
            'game_format_id' => 2,
        ]);
    }

    public function test_admin_can_delete_game_format_type(): void
    {
        $adminUser = User::factory()->admin()->create();
        $gameType = GameType::query()->firstOrFail();
        $gameFormatType = GameFormatType::query()->create([
            'game_type_id' => $gameType->id,
            'game_format_id' => 1,
        ]);

        Livewire::actingAs($adminUser)
            ->test(EditGameFormatType::class, ['record' => $gameFormatType->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing(GameFormatType::class, [
            'id' => $gameFormatType->id,
        ]);
    }

    public function test_game_format_types_list_contains_expected_columns(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(ListGameFormatTypes::class)
            ->assertActionExists('create')
            ->assertTableColumnExists('id')
            ->assertTableColumnExists('gameType.name')
            ->assertTableColumnExists('gameFormat.name')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('updated_at');
    }
}
