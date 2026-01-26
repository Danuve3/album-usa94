<?php

namespace Tests\Feature;

use App\Models\Pack;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AssignDailyPacksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('packs_per_day', 5);
    }

    public function test_command_assigns_packs_to_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->artisan('packs:daily')->assertSuccessful();

        $this->assertDatabaseCount('packs', 10);
        $this->assertEquals(5, Pack::where('user_id', $user1->id)->count());
        $this->assertEquals(5, Pack::where('user_id', $user2->id)->count());
    }

    public function test_command_does_not_duplicate_if_already_assigned_today(): void
    {
        $user = User::factory()->create();

        Pack::factory()->count(5)->create(['user_id' => $user->id]);

        $this->artisan('packs:daily')->assertSuccessful();

        $this->assertDatabaseCount('packs', 5);
    }

    public function test_command_assigns_remaining_packs_if_partial_assignment(): void
    {
        $user = User::factory()->create();

        Pack::factory()->count(3)->create(['user_id' => $user->id]);

        $this->artisan('packs:daily')->assertSuccessful();

        $this->assertDatabaseCount('packs', 5);
        $this->assertEquals(5, Pack::where('user_id', $user->id)->count());
    }

    public function test_command_uses_configurable_packs_per_day(): void
    {
        Setting::set('packs_per_day', 3);

        $user = User::factory()->create();

        $this->artisan('packs:daily')->assertSuccessful();

        $this->assertEquals(3, Pack::where('user_id', $user->id)->count());
    }

    public function test_command_logs_results(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Daily packs assigned:');
            });

        $this->artisan('packs:daily')->assertSuccessful();
    }

    public function test_command_only_counts_todays_packs(): void
    {
        $user = User::factory()->create();

        Pack::factory()->count(5)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDay(),
        ]);

        $this->artisan('packs:daily')->assertSuccessful();

        $this->assertDatabaseCount('packs', 10);
        $this->assertEquals(10, Pack::where('user_id', $user->id)->count());
    }

    public function test_command_outputs_processing_info(): void
    {
        User::factory()->create();

        $this->artisan('packs:daily')
            ->expectsOutputToContain('Daily packs assigned:')
            ->assertSuccessful();
    }
}
