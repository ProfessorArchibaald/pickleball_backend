<?php

namespace Database\Seeders;

use Database\Seeders\Dictionary\GameFormatSeeder;
use Database\Seeders\Dictionary\GameTypeSeeder;
use Database\Seeders\Dictionary\UserRoleSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            GameFormatSeeder::class,
            GameTypeSeeder::class,
        ]);
    }
}
