<?php

namespace Tests\Feature\Database;

use App\Models\Dictionary\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultUserRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_user_roles_exist_after_migrations(): void
    {
        $roleNames = UserRole::query()->orderBy('name')->pluck('name')->all();

        $this->assertCount(2, $roleNames);
        $this->assertSame([UserRole::ADMIN, UserRole::USER], $roleNames);
    }
}
