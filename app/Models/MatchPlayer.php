<?php

namespace App\Models;

use Database\Factories\MatchPlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $match_id
 * @property int $user_id
 * @property int $team
 * @property bool $is_creator
 * @property GameMatch $gameMatch
 * @property User $user
 * @property Collection<int, MatchPoint> $servedMatchPoints
 * @property Collection<int, MatchPoint> $wonMatchPoints
 */
#[Guarded([])]
class MatchPlayer extends Model
{
    /** @use HasFactory<MatchPlayerFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'team' => 'integer',
            'is_creator' => 'boolean',
        ];
    }

    public function gameMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function servedMatchPoints(): HasMany
    {
        return $this->hasMany(MatchPoint::class, 'serve_player_id');
    }

    public function wonMatchPoints(): HasMany
    {
        return $this->hasMany(MatchPoint::class, 'win_point_player_id');
    }
}
