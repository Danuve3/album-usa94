<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TradeRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Trade $trade
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
        $receiverName = $this->trade->receiver->name;
        $offeredCount = $this->trade->offeredItems()->count();
        $requestedCount = $this->trade->requestedItems()->count();

        return [
            'trade_id' => $this->trade->id,
            'type' => 'trade_rejected',
            'message' => "{$receiverName} ha rechazado tu propuesta de intercambio ({$offeredCount} cromo(s) por {$requestedCount}).",
            'receiver_name' => $receiverName,
            'offered_count' => $offeredCount,
            'requested_count' => $requestedCount,
        ];
    }
}
