<?php

namespace Database\Factories\Dictionary;

use App\Models\Dictionary\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserRole>
 */
class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function admin(): static
    {
        return $this->state(['name' => UserRole::ADMIN]);
    }

    public function user(): static
    {
        return $this->state(['name' => UserRole::USER]);
    }
}
