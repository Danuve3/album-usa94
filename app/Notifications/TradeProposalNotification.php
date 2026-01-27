<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TradeProposalNotification extends Notification implements ShouldQueue
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
        $senderName = $this->trade->sender->name;
        $offeredCount = $this->trade->offeredItems()->count();
        $requestedCount = $this->trade->requestedItems()->count();

        return [
            'trade_id' => $this->trade->id,
            'type' => 'trade_proposal',
            'message' => "{$senderName} te ha enviado una propuesta de intercambio: ofrece {$offeredCount} cromo(s) por {$requestedCount} tuyo(s).",
            'sender_name' => $senderName,
            'offered_count' => $offeredCount,
            'requested_count' => $requestedCount,
        ];
    }
}
