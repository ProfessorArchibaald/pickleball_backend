<?php

namespace Tests\Feature\Api;

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
}
