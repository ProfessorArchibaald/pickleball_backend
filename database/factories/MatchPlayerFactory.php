<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchPlayer>
 */
class MatchPlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'match_id' => GameMatch::factory(),
            'user_id' => User::factory(),
            'team' => fake()->numberBetween(1, 2),
            'is_creator' => false,
        ];
    }
}
