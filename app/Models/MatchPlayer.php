<?php

namespace App\Models;

use Database\Factories\MatchPlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $match_id
 * @property int $user_id
 * @property int $team
 * @property bool $is_creator
 * @property GameMatch $gameMatch
 * @property User $user
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
}
