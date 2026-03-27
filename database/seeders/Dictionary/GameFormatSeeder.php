<?php

namespace Database\Seeders\Dictionary;

use App\Models\Dictionary\Game\GameFormat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameFormatSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        DB::table('game_formats')->upsert(
            collect(GameFormat::FORMATS)
                ->map(
                    fn (string $name, int $id): array => [
                        'id' => $id,
                        'name' => $name,
                        'number_of_players' => GameFormat::NUMBER_OF_PLAYERS[$id],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ],
                )
                ->values()
                ->all(),
            ['id'],
            ['name', 'number_of_players', 'updated_at'],
        );
    }
}
