<?php

namespace Tests\Feature;

use App\Models\Pack;
use App\Models\Setting;
use App\Models\User;
use App\Services\PackDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackDeliveryServiceTest extends TestCase
{
    use RefreshDatabase;

    private PackDeliveryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PackDeliveryService();

        // Set up default settings (240 minutes = 4 hours)
        Setting::set('pack_delivery_interval_minutes', 240);
        Setting::set('packs_per_delivery', 1);
    }

    public function test_delivers_packs_based_on_elapsed_time(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(480), // 480 min = 8 hours = 2 deliveries
            'last_pack_received_at' => null,
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(2, $delivered);
        $this->assertEquals(2, Pack::where('user_id', $user->id)->count());
    }

    public function test_does_not_deliver_packs_before_interval(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(120), // Only 2 hours (120 min) < 240 min
            'last_pack_received_at' => null,
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(0, $delivered);
        $this->assertEquals(0, Pack::where('user_id', $user->id)->count());
    }

    public function test_uses_last_pack_received_for_calculation(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subDays(1),
            'last_pack_received_at' => now()->subMinutes(300), // 300 min = 5 hours = 1 delivery
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(1, $delivered);
        $this->assertEquals(1, Pack::where('user_id', $user->id)->count());
    }

    public function test_updates_last_pack_received_timestamp(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(720), // 720 min = 12 hours = 3 deliveries
            'last_pack_received_at' => null,
        ]);

        $this->service->deliverPendingPacks($user);
        $user->refresh();

        $this->assertNotNull($user->last_pack_received_at);
        // Should be 720 minutes after created_at (3 intervals of 240 minutes)
        $this->assertTrue(
            $user->last_pack_received_at->eq($user->created_at->copy()->addMinutes(720))
        );
    }

    public function test_respects_packs_per_delivery_setting(): void
    {
        Setting::set('packs_per_delivery', 3);

        $user = User::factory()->create([
            'created_at' => now()->subMinutes(480), // 2 deliveries
            'last_pack_received_at' => null,
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(6, $delivered); // 2 deliveries * 3 packs
        $this->assertEquals(6, Pack::where('user_id', $user->id)->count());
    }

    public function test_respects_interval_minutes_setting(): void
    {
        Setting::set('pack_delivery_interval_minutes', 120); // 2 hours

        $user = User::factory()->create([
            'created_at' => now()->subMinutes(480), // 480 min / 120 = 4 deliveries
            'last_pack_received_at' => null,
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(4, $delivered);
        $this->assertEquals(4, Pack::where('user_id', $user->id)->count());
    }

    public function test_get_seconds_until_next_pack_returns_correct_value(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(180), // 180 min ago, so 60 min until next (240-180)
            'last_pack_received_at' => null,
        ]);

        $seconds = $this->service->getSecondsUntilNextPack($user);

        // Should be approximately 60 minutes (3600 seconds, with some tolerance)
        $this->assertGreaterThan(3500, $seconds);
        $this->assertLessThanOrEqual(3600, $seconds);
    }

    public function test_get_seconds_returns_zero_when_pack_is_ready(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(300), // 300 min > 240 min interval
            'last_pack_received_at' => null,
        ]);

        $seconds = $this->service->getSecondsUntilNextPack($user);

        $this->assertEquals(0, $seconds);
    }

    public function test_consecutive_calls_do_not_duplicate_packs(): void
    {
        $user = User::factory()->create([
            'created_at' => now()->subMinutes(480),
            'last_pack_received_at' => null,
        ]);

        $first = $this->service->deliverPendingPacks($user);
        $user->refresh();
        $second = $this->service->deliverPendingPacks($user);

        $this->assertEquals(2, $first);
        $this->assertEquals(0, $second);
        $this->assertEquals(2, Pack::where('user_id', $user->id)->count());
    }

    public function test_works_with_short_intervals(): void
    {
        Setting::set('pack_delivery_interval_minutes', 5); // 5 minutes

        $user = User::factory()->create([
            'created_at' => now()->subMinutes(17), // 17 min / 5 = 3 deliveries
            'last_pack_received_at' => null,
        ]);

        $delivered = $this->service->deliverPendingPacks($user);

        $this->assertEquals(3, $delivered);
        $this->assertEquals(3, Pack::where('user_id', $user->id)->count());
    }
}
