<?php

namespace Database\Seeders\Dictionary;

use App\Models\Dictionary\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserRole::defaultNames() as $roleName) {
            UserRole::query()->firstOrCreate(['name' => $roleName]);
        }
    }
}
