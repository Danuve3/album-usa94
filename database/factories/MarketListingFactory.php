<?php

namespace Database\Factories;

use App\Enums\MarketListingStatus;
use App\Models\MarketListing;
use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MarketListing>
 */
class MarketListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = MarketListing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        return [
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'wanted_sticker_id' => null,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ];
    }

    /**
     * Indicate that the listing is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);
    }

    /**
     * Indicate that the listing is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MarketListingStatus::Completed,
        ]);
    }

    /**
     * Indicate that the listing is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MarketListingStatus::Cancelled,
        ]);
    }

    /**
     * Indicate that the listing is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Set a specific wanted sticker.
     */
    public function wanting(Sticker $sticker): static
    {
        return $this->state(fn (array $attributes) => [
            'wanted_sticker_id' => $sticker->id,
        ]);
    }
}
