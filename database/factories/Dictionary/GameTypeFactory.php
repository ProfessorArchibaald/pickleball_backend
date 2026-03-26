<?php

namespace Database\Factories\Dictionary;

use App\Models\Dictionary\GameType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GameType>
 */
class GameTypeFactory extends Factory
{
    protected $model = GameType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => GameType::DEFAULT_NAME,
        ];
    }
}
