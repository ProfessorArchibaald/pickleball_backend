<?php

namespace Tests\Feature\Models;

use App\Models\MatchPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_match_player_belongs_to_match_and_user(): void
    {
        $matchPlayer = MatchPlayer::factory()->create([
            'team' => 1,
            'is_creator' => true,
        ]);

        $this->assertTrue($matchPlayer->relationLoaded('gameMatch') === false);
        $this->assertSame($matchPlayer->match_id, $matchPlayer->gameMatch->id);
        $this->assertSame($matchPlayer->user_id, $matchPlayer->user->id);
        $this->assertSame($matchPlayer->id, $matchPlayer->gameMatch->matchPlayers->first()->id);
        $this->assertSame($matchPlayer->id, $matchPlayer->user->matchPlayers->first()->id);
        $this->assertSame(1, $matchPlayer->team);
        $this->assertTrue($matchPlayer->is_creator);
    }
}
