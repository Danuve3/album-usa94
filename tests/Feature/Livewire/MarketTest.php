<?php

namespace Tests\Feature\Livewire;

use App\Enums\MarketListingStatus;
use App\Livewire\Market;
use App\Models\MarketListing;
use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Market::class)
            ->assertStatus(200);
    }

    public function test_can_search_stickers_by_number(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create(['number' => 123, 'name' => 'Test Sticker']);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->set('searchTerm', '123')
            ->assertSee('Test Sticker');
    }

    public function test_can_search_stickers_by_name(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create(['number' => 1, 'name' => 'Ronaldo']);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->set('searchTerm', 'Ronaldo')
            ->assertSee('Ronaldo');
    }

    public function test_can_select_sticker_filter(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create(['number' => 1]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('selectSticker', $sticker->id)
            ->assertSet('selectedStickerId', $sticker->id)
            ->assertSet('searchTerm', '');
    }

    public function test_can_clear_sticker_filter(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();

        Livewire::actingAs($user)
            ->test(Market::class)
            ->set('selectedStickerId', $sticker->id)
            ->call('clearFilter')
            ->assertSet('selectedStickerId', null)
            ->assertSet('filterType', 'all');
    }

    public function test_can_publish_sticker_to_market(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openPublishModal', $sticker->id)
            ->assertSet('publishStickerId', $sticker->id)
            ->assertSet('showPublishModal', true)
            ->call('publishListing')
            ->assertSet('showSuccessModal', true);

        $this->assertDatabaseHas('market_listings', [
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => 'active',
        ]);
    }

    public function test_cannot_publish_glued_sticker(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->set('publishStickerId', $sticker->id)
            ->call('publishListing');

        $this->assertDatabaseMissing('market_listings', [
            'user_id' => $user->id,
        ]);
    }

    public function test_cannot_publish_sticker_already_listed(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openPublishModal', $sticker->id)
            ->call('publishListing');

        $this->assertEquals(1, MarketListing::where('user_id', $user->id)->count());
    }

    public function test_can_publish_with_wanted_sticker(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $wantedSticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openPublishModal', $sticker->id)
            ->set('wantedStickerId', $wantedSticker->id)
            ->call('publishListing')
            ->assertSet('showSuccessModal', true);

        $this->assertDatabaseHas('market_listings', [
            'user_id' => $user->id,
            'wanted_sticker_id' => $wantedSticker->id,
        ]);
    }

    public function test_can_cancel_own_listing(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('cancelListing', $listing->id);

        $listing->refresh();
        $this->assertEquals(MarketListingStatus::Cancelled, $listing->status);
    }

    public function test_cannot_cancel_other_users_listing(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        $listing = MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('cancelListing', $listing->id);

        $listing->refresh();
        $this->assertEquals(MarketListingStatus::Active, $listing->status);
    }

    public function test_shows_listings_from_other_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $sticker = Sticker::factory()->create(['name' => 'Other User Sticker']);
        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->assertSee('Other User Sticker');
    }

    public function test_does_not_show_own_listings_in_market(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create(['name' => 'My Sticker']);
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $component = Livewire::actingAs($user)->test(Market::class);
        $listings = $component->viewData('listings');

        $this->assertEquals(0, $listings->count());
    }

    public function test_does_not_show_expired_listings(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $sticker = Sticker::factory()->create(['name' => 'Expired Sticker']);
        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->subDay(),
        ]);

        $component = Livewire::actingAs($user)->test(Market::class);
        $listings = $component->viewData('listings');

        $this->assertEquals(0, $listings->count());
    }

    public function test_can_open_trade_modal(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        $listing = MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openTradeModal', $listing->id)
            ->assertSet('showTradeModal', true)
            ->assertSet('selectedListing.id', $listing->id);
    }

    public function test_cannot_trade_with_own_listing(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        $listing = MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $component = Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openTradeModal', $listing->id);

        $component->assertSet('showTradeModal', false);

        $this->assertDatabaseMissing('trades', [
            'sender_id' => $user->id,
            'receiver_id' => $user->id,
        ]);
    }

    public function test_can_toggle_offered_sticker(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('toggleOfferedSticker', $sticker->id)
            ->assertSet('offeredStickerIds', [$sticker->id])
            ->call('toggleOfferedSticker', $sticker->id)
            ->assertSet('offeredStickerIds', []);
    }

    public function test_can_initiate_trade_from_listing(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $userSticker1 = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $userSticker2 = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $listing = MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker2->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        Livewire::actingAs($user)
            ->test(Market::class)
            ->call('openTradeModal', $listing->id)
            ->call('toggleOfferedSticker', $sticker1->id)
            ->call('initiateTradeFromListing')
            ->assertSet('showSuccessModal', true);

        $this->assertDatabaseHas('trades', [
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'status' => 'pending',
        ]);
    }

    public function test_my_duplicates_shows_unglued_stickers(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();

        UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        $component = Livewire::actingAs($user)->test(Market::class);
        $duplicates = $component->viewData('myDuplicates');

        $this->assertCount(1, $duplicates);
        $this->assertEquals($sticker->id, $duplicates[0]['sticker_id']);
    }

    public function test_my_duplicates_does_not_show_glued_stickers(): void
    {
        $user = User::factory()->create();
        $sticker = Sticker::factory()->create();

        UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker->id,
            'is_glued' => true,
        ]);

        $component = Livewire::actingAs($user)->test(Market::class);
        $duplicates = $component->viewData('myDuplicates');

        $this->assertCount(0, $duplicates);
    }

    public function test_shows_listings_i_want(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $sticker = Sticker::factory()->create();
        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $component = Livewire::actingAs($user)->test(Market::class);
        $listingsIWant = $component->viewData('listingsIWant');

        $this->assertCount(1, $listingsIWant);
    }

    public function test_filters_by_sticker_offering(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $userSticker1 = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $userSticker2 = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker1->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker2->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $component = Livewire::actingAs($user)
            ->test(Market::class)
            ->set('selectedStickerId', $sticker1->id)
            ->set('filterType', 'offering');

        $listings = $component->viewData('listings');

        $this->assertCount(1, $listings);
    }

    public function test_filters_by_sticker_wanting(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $wantedSticker = Sticker::factory()->create();

        $userSticker = UserSticker::factory()->create([
            'user_id' => $otherUser->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        MarketListing::create([
            'user_id' => $otherUser->id,
            'user_sticker_id' => $userSticker->id,
            'wanted_sticker_id' => $wantedSticker->id,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $component = Livewire::actingAs($user)
            ->test(Market::class)
            ->set('selectedStickerId', $wantedSticker->id)
            ->set('filterType', 'wanting');

        $listings = $component->viewData('listings');

        $this->assertCount(1, $listings);
    }
}
