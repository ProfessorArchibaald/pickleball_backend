<?php

namespace App\Models;

use App\Models\Dictionary\GameType;
use Database\Factories\GameMatchFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $game_type_id
 * @property Carbon $created_at
 * @property Carbon|null $finished_at
 * @property GameType $gameType
 */
#[Guarded([])]
class GameMatch extends Model
{
    /** @use HasFactory<GameMatchFactory> */
    use HasFactory;

    public const null UPDATED_AT = null;

    protected $table = 'matches';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function gameType(): BelongsTo
    {
        return $this->belongsTo(GameType::class);
    }
}
