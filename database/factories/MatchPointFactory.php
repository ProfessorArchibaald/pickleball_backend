<?php

namespace Database\Factories;

use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\MatchPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MatchPoint>
 */
class MatchPointFactory extends Factory
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
            'serve_player_id' => fn (array $attributes): int => MatchPlayer::factory()->create([
                'match_id' => $attributes['match_id'],
            ])->id,
            'team1_score' => fake()->numberBetween(0, 21),
            'team2_score' => fake()->numberBetween(0, 21),
            'win_point_player_id' => null,
        ];
    }

    public function withWinner(): static
    {
        return $this->state(fn (array $attributes): array => [
            'win_point_player_id' => MatchPlayer::factory()->create([
                'match_id' => $attributes['match_id'],
            ])->id,
        ]);
    }
}
