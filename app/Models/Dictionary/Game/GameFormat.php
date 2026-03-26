<?php

namespace App\Models\Dictionary\Game;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 */
#[Fillable(['id', 'name'])]
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

        static::updating(static function (): void {
            throw ValidationException::withMessages([
                'game_format' => 'Game formats cannot be updated.',
            ]);
        });

        static::deleting(static function (): void {
            throw ValidationException::withMessages([
                'game_format' => 'Game formats cannot be deleted.',
            ]);
        });
    }
}
