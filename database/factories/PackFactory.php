<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pack>
 */
class PackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'opened_at' => null,
        ];
    }

    /**
     * Indicate that the pack has been opened.
     */
    public function opened(): static
    {
        return $this->state(fn (array $attributes) => [
            'opened_at' => now(),
        ]);
    }
}
