<?php

namespace Tests\Unit;

use App\Enums\MarketListingStatus;
use App\Models\MarketListing;
use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_market_listing(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $this->assertDatabaseHas('market_listings', [
            'id' => $listing->id,
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $this->assertEquals($user->id, $listing->user->id);
    }

    public function test_belongs_to_user_sticker(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $this->assertEquals($userSticker->id, $listing->userSticker->id);
    }

    public function test_can_have_wanted_sticker(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $wantedSticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'wanted_sticker_id' => $wantedSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $this->assertEquals($wantedSticker->id, $listing->wantedSticker->id);
    }

    public function test_is_active_returns_true_for_active_non_expired(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $this->assertTrue($listing->isActive());
    }

    public function test_is_active_returns_false_for_expired(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse($listing->isActive());
    }

    public function test_is_active_returns_false_for_cancelled(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Cancelled,
        ]);

        $this->assertFalse($listing->isActive());
    }

    public function test_can_transition_from_active_to_completed(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $result = $listing->transitionTo(MarketListingStatus::Completed);

        $this->assertTrue($result);
        $this->assertEquals(MarketListingStatus::Completed, $listing->status);
    }

    public function test_can_transition_from_active_to_cancelled(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $result = $listing->transitionTo(MarketListingStatus::Cancelled);

        $this->assertTrue($result);
        $this->assertEquals(MarketListingStatus::Cancelled, $listing->status);
    }

    public function test_cannot_transition_from_completed_to_active(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Completed,
        ]);

        $result = $listing->transitionTo(MarketListingStatus::Active);

        $this->assertFalse($result);
        $this->assertEquals(MarketListingStatus::Completed, $listing->status);
    }

    public function test_active_scope_filters_correctly(): void
    {
        $user = User::factory()->create();
        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();
        $sticker3 = Sticker::factory()->create();

        $userSticker1 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker1->id,
        ]);
        $userSticker2 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker2->id,
        ]);
        $userSticker3 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker3->id,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker1->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker2->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->subDay(),
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker3->id,
            'status' => MarketListingStatus::Cancelled,
        ]);

        $activeListings = MarketListing::active()->get();

        $this->assertCount(1, $activeListings);
    }

    public function test_offering_scope_filters_by_sticker(): void
    {
        $user = User::factory()->create();
        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $userSticker1 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker1->id,
        ]);
        $userSticker2 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker2->id,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker1->id,
            'status' => MarketListingStatus::Active,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker2->id,
            'status' => MarketListingStatus::Active,
        ]);

        $listings = MarketListing::offering($sticker1->id)->get();

        $this->assertCount(1, $listings);
    }

    public function test_wanting_scope_filters_by_wanted_sticker(): void
    {
        $user = User::factory()->create();
        $sticker1 = Sticker::factory()->create();
        $wantedSticker = Sticker::factory()->create();

        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker1->id,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'wanted_sticker_id' => $wantedSticker->id,
            'status' => MarketListingStatus::Active,
        ]);

        $listings = MarketListing::wanting($wantedSticker->id)->get();

        $this->assertCount(1, $listings);
    }

    public function test_exclude_user_scope(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $userSticker1 = UserSticker::factory()->create([
            'user_id' => $user1->id,
            'sticker_id' => $sticker1->id,
        ]);
        $userSticker2 = UserSticker::factory()->create([
            'user_id' => $user2->id,
            'sticker_id' => $sticker2->id,
        ]);

        MarketListing::create([
            'user_id' => $user1->id,
            'user_sticker_id' => $userSticker1->id,
            'status' => MarketListingStatus::Active,
        ]);

        MarketListing::create([
            'user_id' => $user2->id,
            'user_sticker_id' => $userSticker2->id,
            'status' => MarketListingStatus::Active,
        ]);

        $listings = MarketListing::excludeUser($user1->id)->get();

        $this->assertCount(1, $listings);
        $this->assertEquals($user2->id, $listings->first()->user_id);
    }
}
