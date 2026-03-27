<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_fetch_players_list(): void
    {
        $response = $this->getJson('/api/players');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_fetch_all_other_players_without_pagination(): void
    {
        $authenticatedUser = User::factory()->create([
            'name' => 'Auth',
            'last_name' => 'User',
        ]);
        $token = $authenticatedUser->createToken('iphone-16')->plainTextToken;

        $firstPlayer = User::factory()->create([
            'name' => 'John',
            'last_name' => 'Player',
        ]);
        $secondPlayer = User::factory()->create([
            'name' => 'Jane',
            'last_name' => 'Coach',
        ]);

        $response = $this->withToken($token)->getJson('/api/players');

        $response->assertOk()->assertExactJson([
            'data' => [
                [
                    'id' => $firstPlayer->id,
                    'full_name' => 'John Player',
                ],
                [
                    'id' => $secondPlayer->id,
                    'full_name' => 'Jane Coach',
                ],
            ],
        ]);
    }

    public function test_players_list_does_not_include_the_authenticated_user(): void
    {
        $authenticatedUser = User::factory()->create([
            'name' => 'Hidden',
            'last_name' => 'Self',
        ]);
        $token = $authenticatedUser->createToken('iphone-16')->plainTextToken;
        $otherPlayer = User::factory()->create([
            'name' => 'Visible',
            'last_name' => 'Player',
        ]);

        $response = $this->withToken($token)->getJson('/api/players');

        $response
            ->assertOk()
            ->assertJsonMissing([
                'id' => $authenticatedUser->id,
                'full_name' => 'Hidden Self',
            ])
            ->assertJsonPath('data.0.id', $otherPlayer->id)
            ->assertJsonPath('data.0.full_name', 'Visible Player');
    }

    public function test_full_name_does_not_include_extra_spaces_when_last_name_is_missing(): void
    {
        $authenticatedUser = User::factory()->create();
        $token = $authenticatedUser->createToken('iphone-16')->plainTextToken;
        $userWithoutLastName = User::factory()->create([
            'name' => 'Single',
            'last_name' => null,
        ]);

        $response = $this->withToken($token)->getJson('/api/players');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $userWithoutLastName->id)
            ->assertJsonPath('data.0.full_name', 'Single');
    }
}
