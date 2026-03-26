<?php

namespace App\Models;

use App\Models\Dictionary\Game\GameFormat;
use App\Models\Dictionary\Game\GameType;
use Database\Factories\GameMatchFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $game_type_id
 * @property int $game_format_id
 * @property Carbon $created_at
 * @property Carbon|null $finished_at
 * @property int|null $duration
 * @property GameType $gameType
 * @property GameFormat $gameFormat
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
            'duration' => 'integer',
        ];
    }

    public function gameType(): BelongsTo
    {
        return $this->belongsTo(GameType::class);
    }

    public function gameFormat(): BelongsTo
    {
        return $this->belongsTo(GameFormat::class);
    }
}
