<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TradeAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Trade $trade,
        public bool $isSender
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $otherUser = $this->isSender
            ? $this->trade->receiver->name
            : $this->trade->sender->name;

        $offeredCount = $this->trade->offeredItems()->count();
        $requestedCount = $this->trade->requestedItems()->count();

        return [
            'trade_id' => $this->trade->id,
            'type' => 'trade_accepted',
            'message' => $this->isSender
                ? "Tu intercambio con {$otherUser} ha sido aceptado. Recibiste {$requestedCount} cromo(s) y entregaste {$offeredCount}."
                : "Has aceptado el intercambio con {$otherUser}. Recibiste {$offeredCount} cromo(s) y entregaste {$requestedCount}.",
            'other_user' => $otherUser,
            'offered_count' => $offeredCount,
            'requested_count' => $requestedCount,
        ];
    }
}
