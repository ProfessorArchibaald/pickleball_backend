<?php

namespace Tests\Feature\Models;

use App\Models\MatchPlayer;
use App\Models\MatchPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchPointTest extends TestCase
{
    use RefreshDatabase;

    public function test_match_point_belongs_to_match_and_match_players(): void
    {
        $servePlayer = MatchPlayer::factory()->create();
        $winPointPlayer = MatchPlayer::factory()->create([
            'match_id' => $servePlayer->match_id,
        ]);

        $matchPoint = MatchPoint::factory()->create([
            'match_id' => $servePlayer->match_id,
            'serve_player_id' => $servePlayer->id,
            'team1_score' => 8,
            'team2_score' => 6,
            'win_point_player_id' => $winPointPlayer->id,
        ]);

        $this->assertTrue($matchPoint->relationLoaded('gameMatch') === false);
        $this->assertSame($matchPoint->match_id, $matchPoint->gameMatch->id);
        $this->assertSame($servePlayer->id, $matchPoint->servePlayer->id);
        $this->assertSame($winPointPlayer->id, $matchPoint->winPointPlayer->id);
        $this->assertSame($matchPoint->id, $matchPoint->gameMatch->matchPoints->first()->id);
        $this->assertSame($matchPoint->id, $servePlayer->servedMatchPoints->first()->id);
        $this->assertSame($matchPoint->id, $winPointPlayer->wonMatchPoints->first()->id);
        $this->assertSame(8, $matchPoint->team1_score);
        $this->assertSame(6, $matchPoint->team2_score);
    }
}
