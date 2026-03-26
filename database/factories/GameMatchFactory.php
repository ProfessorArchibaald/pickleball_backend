<?php

namespace Database\Factories;

use App\Models\Dictionary\Game\GameType;
use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameMatch>
 */
class GameMatchFactory extends Factory
{
    protected $model = GameMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'game_type_id' => fn (): int => GameType::query()->firstOrFail()->getKey(),
            'finished_at' => null,
            'duration' => null,
        ];
    }
}
