<?php

namespace Database\Factories;

use App\Enums\StickerRarity;
use App\Models\Sticker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sticker>
 */
class StickerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Sticker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 444),
            'name' => fake()->name(),
            'page_number' => fake()->numberBetween(1, 60),
            'position_x' => fake()->numberBetween(0, 800),
            'position_y' => fake()->numberBetween(0, 600),
            'width' => fake()->numberBetween(80, 150),
            'height' => fake()->numberBetween(100, 180),
            'is_horizontal' => fake()->boolean(20),
            'rarity' => fake()->randomElement(StickerRarity::cases()),
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the sticker is shiny.
     */
    public function shiny(): static
    {
        return $this->state(fn (array $attributes) => [
            'rarity' => StickerRarity::Shiny,
        ]);
    }

    /**
     * Indicate that the sticker is horizontal.
     */
    public function horizontal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_horizontal' => true,
        ]);
    }
}
