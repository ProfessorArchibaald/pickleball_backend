<?php

namespace Tests\Feature\Api;

use App\Models\GameMatch;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchApiTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(?User $user = null): array
    {
        $user ??= User::factory()->create();

        $token = $user->createToken('test-device')->plainTextToken;

        return [
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ];
    }

    public function test_guest_cannot_create_a_match(): void
    {
        $response = $this->postJson('/api/matches');

        $response->assertUnauthorized();
    }

    public function test_match_can_be_created(): void
    {
        $response = $this->postJson('/api/matches', [], $this->authHeaders());

        $response
            ->assertCreated()
            ->assertJsonPath('data.finished_at', null)
            ->assertJsonPath('data.is_finished', false);

        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseHas('matches', [
            'id' => $response->json('data.id'),
            'finished_at' => null,
        ]);
    }

    public function test_match_can_be_finished(): void
    {
        $match = GameMatch::factory()->create();

        $response = $this->patchJson("/api/matches/{$match->id}/finish", [], $this->authHeaders());

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $match->id)
            ->assertJsonPath('data.is_finished', true);

        $this->assertNotNull($match->fresh()->finished_at);
    }

    public function test_finishing_an_already_finished_match_keeps_the_original_finished_at(): void
    {
        $finishedAt = CarbonImmutable::parse('2026-03-24 10:00:00');

        $match = GameMatch::factory()->create([
            'finished_at' => $finishedAt,
        ]);

        $response = $this->patchJson("/api/matches/{$match->id}/finish", [], $this->authHeaders());

        $response
            ->assertOk()
            ->assertJsonPath('data.is_finished', true)
            ->assertJsonPath('data.finished_at', $finishedAt->toISOString());

        $freshFinishedAt = $match->fresh()?->finished_at;
        $this->assertNotNull($freshFinishedAt);
        $this->assertTrue($freshFinishedAt->equalTo($finishedAt));
    }

    public function test_guest_cannot_finish_a_match(): void
    {
        $match = GameMatch::factory()->create();

        $response = $this->patchJson("/api/matches/{$match->id}/finish");

        $response->assertUnauthorized();
    }
}
