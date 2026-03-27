<?php

namespace App\Models;

use Database\Factories\MatchPointFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $match_id
 * @property int $serve_player_id
 * @property int $team1_score
 * @property int $team2_score
 * @property int|null $win_point_player_id
 * @property GameMatch $gameMatch
 * @property MatchPlayer $servePlayer
 * @property MatchPlayer $winPointPlayer
 */
#[Guarded([])]
class MatchPoint extends Model
{
    /** @use HasFactory<MatchPointFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'team1_score' => 'integer',
            'team2_score' => 'integer',
        ];
    }

    public function gameMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function servePlayer(): BelongsTo
    {
        return $this->belongsTo(MatchPlayer::class, 'serve_player_id');
    }

    public function winPointPlayer(): BelongsTo
    {
        return $this->belongsTo(MatchPlayer::class, 'win_point_player_id');
    }
}
