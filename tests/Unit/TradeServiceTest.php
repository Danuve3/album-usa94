<?php

namespace Tests\Unit;

use App\Enums\TradeItemDirection;
use App\Enums\TradeStatus;
use App\Models\Sticker;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\User;
use App\Models\UserSticker;
use App\Notifications\TradeAcceptedNotification;
use App\Services\TradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TradeServiceTest extends TestCase
{
    use RefreshDatabase;

    private TradeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TradeService;
    }

    public function test_executes_trade_successfully(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->service->execute($trade);

        $trade->refresh();
        $senderSticker->refresh();
        $receiverSticker->refresh();

        $this->assertEquals(TradeStatus::Accepted, $trade->status);
        $this->assertEquals($receiver->id, $senderSticker->user_id);
        $this->assertEquals($sender->id, $receiverSticker->user_id);
    }

    public function test_notifies_both_parties(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->service->execute($trade);

        Notification::assertSentTo($sender, TradeAcceptedNotification::class);
        Notification::assertSentTo($receiver, TradeAcceptedNotification::class);
    }

    public function test_throws_exception_for_non_pending_trade(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Accepted,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El intercambio ya no está pendiente.');

        $this->service->execute($trade);
    }

    public function test_throws_exception_for_expired_trade(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => TradeStatus::Pending,
            'expires_at' => now()->subDay(),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El intercambio ha expirado.');

        $this->service->execute($trade);
    }

    public function test_throws_exception_when_sender_no_longer_owns_sticker(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $thirdParty = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $thirdParty->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El remitente ya no posee algunos cromos ofrecidos.');

        $this->service->execute($trade);
    }

    public function test_throws_exception_when_receiver_no_longer_owns_sticker(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $thirdParty = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $thirdParty->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El destinatario ya no posee algunos cromos solicitados.');

        $this->service->execute($trade);
    }

    public function test_throws_exception_when_offered_sticker_is_glued(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => true,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => false,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Algunos cromos ofrecidos están pegados y no se pueden intercambiar.');

        $this->service->execute($trade);
    }

    public function test_throws_exception_when_requested_sticker_is_glued(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => true,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Algunos cromos solicitados están pegados y no se pueden intercambiar.');

        $this->service->execute($trade);
    }

    public function test_does_not_modify_trade_when_validation_fails(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $sticker1 = Sticker::factory()->create();
        $sticker2 = Sticker::factory()->create();

        $senderSticker = UserSticker::factory()->create([
            'user_id' => $sender->id,
            'sticker_id' => $sticker1->id,
            'is_glued' => false,
        ]);

        $receiverSticker = UserSticker::factory()->create([
            'user_id' => $receiver->id,
            'sticker_id' => $sticker2->id,
            'is_glued' => true,
        ]);

        $trade = Trade::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
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

        try {
            $this->service->execute($trade);
        } catch (\InvalidArgumentException $e) {
            // Expected
        }

        $senderSticker->refresh();
        $trade->refresh();

        $this->assertEquals($sender->id, $senderSticker->user_id);
        $this->assertEquals(TradeStatus::Pending, $trade->status);
    }
}
