<?php

namespace Tests\Feature\Livewire;

use App\Livewire\NotificationBell;
use App\Models\User;
use App\Notifications\TradeProposalNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->assertStatus(200);
    }

    public function test_shows_unread_count(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = \App\Models\Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => \App\Enums\TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        $user->notifyNow(new TradeProposalNotification($trade));

        $component = Livewire::actingAs($user)->test(NotificationBell::class);
        $this->assertEquals(1, $component->viewData('unreadCount'));
    }

    public function test_can_toggle_dropdown(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->assertSet('showDropdown', false)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', true)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', false);
    }

    public function test_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = \App\Models\Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => \App\Enums\TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        $user->notifyNow(new TradeProposalNotification($trade));

        $notification = $user->notifications()->first();
        $this->assertNull($notification->read_at);

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAsRead', $notification->id);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_can_mark_all_as_read(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = \App\Models\Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => \App\Enums\TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        $user->notifyNow(new TradeProposalNotification($trade));
        $user->notifyNow(new TradeProposalNotification($trade));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAllAsRead');

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_shows_empty_state_when_no_notifications(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('toggleDropdown')
            ->assertSee('No tienes notificaciones');
    }

    public function test_shows_notification_message(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create(['name' => 'Juan Perez']);

        $trade = \App\Models\Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => \App\Enums\TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        $user->notifyNow(new TradeProposalNotification($trade));

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('toggleDropdown')
            ->assertSee('Juan Perez');
    }

    public function test_can_close_dropdown(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', true)
            ->call('closeDropdown')
            ->assertSet('showDropdown', false);
    }
}
