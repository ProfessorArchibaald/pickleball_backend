<?php

namespace App\Models\Dictionary;

use App\Models\User;
use Database\Factories\Dictionary\UserRoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $name
 */
#[Fillable(['name'])]
class UserRole extends Model
{
    /** @use HasFactory<UserRoleFactory> */
    use HasFactory;

    protected $table = 'user_roles';

    public const string ADMIN = 'Admin';

    public const string USER = 'User';

    protected static function booted(): void
    {
        static::updating(function (self $userRole): void {
            if ($userRole->wasDefaultRole() && $userRole->isDirty('name')) {
                throw ValidationException::withMessages([
                    'name' => 'The default user roles cannot be renamed.',
                ]);
            }
        });

        static::deleting(function (self $userRole): void {
            if ($userRole->isDefaultRole()) {
                throw ValidationException::withMessages([
                    'role' => 'The default user roles cannot be deleted.',
                ]);
            }
        });
    }

    /**
     * @return list<string>
     */
    public static function defaultNames(): array
    {
        return [self::ADMIN, self::USER];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }

    public function isDefaultRole(): bool
    {
        return in_array($this->name, self::defaultNames(), true);
    }

    public function wasDefaultRole(): bool
    {
        return in_array($this->getOriginal('name', ''), self::defaultNames(), true);
    }
}
