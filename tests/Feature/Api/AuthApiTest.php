<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_receive_a_bearer_token(): void
    {
        $user = User::factory()->create([
            'email' => 'player@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'player@example.com',
            'password' => 'password',
            'device_name' => 'iphone-16',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.email', $user->email);

        $plainTextToken = $response->json('data.token');

        $this->assertNotEmpty($plainTextToken);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'iphone-16',
        ]);
    }

    public function test_login_requires_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'player@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'player@example.com',
            'password' => 'wrong-password',
            'device_name' => 'iphone-16',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_authenticated_user_can_fetch_their_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('iphone-16')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/auth/user');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_the_current_access_token(): void
    {
        $user = User::factory()->create();
        $plainTextToken = $user->createToken('iphone-16')->plainTextToken;
        $tokenId = explode('|', $plainTextToken, 2)[0];

        $response = $this->withToken($plainTextToken)->postJson('/api/auth/logout');

        $response->assertNoContent();

        $this->assertNull(PersonalAccessToken::query()->find($tokenId));
    }
}
