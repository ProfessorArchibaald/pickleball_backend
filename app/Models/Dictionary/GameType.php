<?php

namespace App\Models\Dictionary;

use Database\Factories\Dictionary\GameTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 */
#[Fillable(['name'])]
class GameType extends Model
{
    /** @use HasFactory<GameTypeFactory> */
    use HasFactory;

    protected $table = 'game_types';

    public const string DEFAULT_NAME = 'Pickleball';

    protected static function booted(): void
    {
        static::creating(static function (): void {
            if (static::query()->exists()) {
                throw ValidationException::withMessages([
                    'game_type' => 'Only one game type can exist.',
                ]);
            }
        });

        static::deleting(static function (): void {
            throw ValidationException::withMessages([
                'game_type' => 'Game types cannot be deleted.',
            ]);
        });
    }
}
