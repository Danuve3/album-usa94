<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 60),
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the page has an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'pages/page-'.$attributes['number'].'.jpg',
        ]);
    }
}
