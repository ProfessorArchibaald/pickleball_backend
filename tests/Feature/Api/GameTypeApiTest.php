<?php

namespace Tests\Feature\Api;

use App\Models\Dictionary\Game\GameFormatType;
use App\Models\Dictionary\Game\GameType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTypeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_get_game_types_dictionary(): void
    {
        $response = $this->getJson('/api/game-types');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', GameType::DEFAULT_NAME);
    }

    public function test_game_types_api_returns_expected_shape(): void
    {
        $gameType = GameType::query()->firstOrFail();

        $response = $this->getJson('/api/game-types');

        $response->assertOk()->assertExactJson([
            'data' => [
                [
                    'id' => $gameType->id,
                    'name' => $gameType->name,
                ],
            ],
        ]);
    }

    public function test_game_type_formats_api_returns_an_empty_array_when_no_formats_are_linked(): void
    {
        $gameType = GameType::query()->firstOrFail();

        $response = $this->getJson("/api/game-types/{$gameType->id}/formats");

        $response->assertOk()->assertExactJson([
            'data' => [],
        ]);
    }

    public function test_game_type_formats_api_returns_available_formats_for_a_game_type(): void
    {
        $gameType = GameType::query()->firstOrFail();

        GameFormatType::query()->create([
            'game_type_id' => $gameType->id,
            'game_format_id' => 2,
        ]);

        $response = $this->getJson("/api/game-types/{$gameType->id}/formats");

        $response->assertOk()->assertExactJson([
            'data' => [
                [
                    'id' => 2,
                    'name' => '2x2',
                ],
            ],
        ]);
    }

    public function test_game_type_formats_api_returns_not_found_for_an_unknown_game_type(): void
    {
        $response = $this->getJson('/api/game-types/999/formats');

        $response->assertNotFound();
    }
}
