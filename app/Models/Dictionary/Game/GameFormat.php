<?php

namespace App\Models\Dictionary\Game;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 * @property int $number_of_players
 */
#[Fillable(['name', 'number_of_players'])]
class GameFormat extends Model
{
    protected $table = 'game_formats';

    /**
     * @var array<int, string>
     */
    public const array FORMATS = [
        1 => '1x1',
        2 => '2x2',
        3 => '1x2',
    ];

    /**
     * @var array<int, int>
     */
    public const array NUMBER_OF_PLAYERS = [
        1 => 2,
        2 => 4,
        3 => 3,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'number_of_players' => 'integer',
        ];
    }

    public function gameFormatTypes(): HasMany
    {
        return $this->hasMany(GameFormatType::class);
    }

    protected static function booted(): void
    {
        static::creating(static function (): void {
            throw ValidationException::withMessages([
                'game_format' => 'Game formats cannot be created manually.',
            ]);
        });

        static::updating(static function (GameFormat $gameFormat): void {
            if (array_diff(array_keys($gameFormat->getDirty()), ['number_of_players', 'updated_at']) !== []) {
                throw ValidationException::withMessages([
                    'game_format' => 'Only the number of players can be updated for game formats.',
                ]);
            }
        });

        static::deleting(static function (): void {
            throw ValidationException::withMessages([
                'game_format' => 'Game formats cannot be deleted.',
            ]);
        });
    }
}
