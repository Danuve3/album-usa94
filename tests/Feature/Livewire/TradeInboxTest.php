<?php

namespace Tests\Feature\Livewire;

use App\Enums\TradeItemDirection;
use App\Enums\TradeStatus;
use App\Livewire\TradeInbox;
use App\Models\Sticker;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\User;
use App\Models\UserSticker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TradeInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->assertStatus(200);
    }

    public function test_shows_received_trades(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create(['name' => 'Sender User']);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->assertSet('activeTab', 'received')
            ->assertSee('Sender User');
    }

    public function test_shows_sent_trades(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create(['name' => 'Receiver User']);

        $trade = Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('setTab', 'sent')
            ->assertSet('activeTab', 'sent')
            ->assertSee('Receiver User');
    }

    public function test_can_select_trade(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        $component = Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->assertSet('selectedTradeId', $trade->id);

        $this->assertNotNull($component->get('selectedTrade'));
    }

    public function test_can_close_detail(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->call('closeDetail')
            ->assertSet('selectedTradeId', null)
            ->assertSet('selectedTrade', null);
    }

    public function test_can_accept_trade(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $user->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        TradeItem::create([
            'trade_id' => $trade->id,
            'user_sticker_id' => $senderSticker->id,
            'direction' => TradeItemDirection::Offered,
        ]);

        TradeItem::create([
            'trade_id' => $trade->id,
            'user_sticker_id' => $receiverSticker->id,
            'direction' => TradeItemDirection::Requested,
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'accept')
            ->assertSet('showConfirmModal', true)
            ->assertSet('confirmAction', 'accept')
            ->call('executeAction');

        $trade->refresh();
        $senderSticker->refresh();
        $receiverSticker->refresh();

        $this->assertEquals(TradeStatus::Accepted, $trade->status);
        $this->assertEquals($user->id, $senderSticker->user_id);
        $this->assertEquals($sender->id, $receiverSticker->user_id);
    }

    public function test_cannot_accept_trade_as_sender(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('setTab', 'sent')
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'accept')
            ->call('executeAction');

        $trade->refresh();
        $this->assertEquals(TradeStatus::Pending, $trade->status);
    }

    public function test_can_reject_trade(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'reject')
            ->call('executeAction');

        $trade->refresh();
        $this->assertEquals(TradeStatus::Rejected, $trade->status);
    }

    public function test_can_cancel_trade(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('setTab', 'sent')
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'cancel')
            ->call('executeAction');

        $trade->refresh();
        $this->assertEquals(TradeStatus::Cancelled, $trade->status);
    }

    public function test_cannot_cancel_trade_as_receiver(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'cancel')
            ->call('executeAction');

        $trade->refresh();
        $this->assertEquals(TradeStatus::Pending, $trade->status);
    }

    public function test_pending_received_count(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Accepted,
            'expires_at' => now()->addDays(7),
        ]);

        $component = Livewire::actingAs($user)->test(TradeInbox::class);
        $count = $component->viewData('pendingReceivedCount');

        $this->assertEquals(2, $count);
    }

    public function test_pending_sent_count(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->addDays(7),
        ]);

        Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Rejected,
            'expires_at' => now()->addDays(7),
        ]);

        $component = Livewire::actingAs($user)->test(TradeInbox::class);
        $count = $component->viewData('pendingSentCount');

        $this->assertEquals(1, $count);
    }

    public function test_cannot_accept_expired_trade(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->subDay(),
        ]);

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('selectTrade', $trade->id)
            ->call('confirmAction', 'accept')
            ->call('executeAction');

        $trade->refresh();
        $this->assertEquals(TradeStatus::Pending, $trade->status);
    }

    public function test_shows_empty_state_for_no_received_trades(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->assertSee('No hay propuestas recibidas');
    }

    public function test_shows_empty_state_for_no_sent_trades(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TradeInbox::class)
            ->call('setTab', 'sent')
            ->assertSee('No has enviado propuestas');
    }
}
