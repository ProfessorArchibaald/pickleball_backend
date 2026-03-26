<?php

namespace Database\Seeders\Dictionary;

use App\Models\Dictionary\GameType;
use Illuminate\Database\Seeder;

class GameTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (GameType::query()->exists()) {
            return;
        }

        GameType::query()->create([
            'name' => GameType::DEFAULT_NAME,
        ]);
    }
}
