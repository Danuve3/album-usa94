<?php

namespace Database\Factories;

use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSticker>
 */
class UserStickerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = UserSticker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sticker_id' => Sticker::factory(),
            'is_glued' => false,
            'obtained_at' => now(),
        ];
    }

    /**
     * Indicate that the sticker is glued.
     */
    public function glued(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_glued' => true,
        ]);
    }

    /**
     * Indicate that the sticker is unglued.
     */
    public function unglued(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_glued' => false,
        ]);
    }
}
