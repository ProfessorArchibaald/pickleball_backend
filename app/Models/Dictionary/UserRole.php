<?php

namespace App\Models\Dictionary;

use App\Models\User;
use Database\Factories\Dictionary\UserRoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function isAdmin(): bool
    {
        return $this->name === self::ADMIN;
    }
}
