<?php

namespace App\Models\Dictionary\Game;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_type_id
 * @property int $game_format_id
 * @property GameType $gameType
 * @property GameFormat $gameFormat
 */
#[Guarded([])]
class GameFormatType extends Model
{
    protected $table = 'game_format_types';

    public function gameType(): BelongsTo
    {
        return $this->belongsTo(GameType::class);
    }

    public function gameFormat(): BelongsTo
    {
        return $this->belongsTo(GameFormat::class);
    }
}
