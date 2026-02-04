<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PacksGiftedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $packsCount,
        public ?string $message = null
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
        $defaultMessage = $this->packsCount === 1
            ? "Has recibido 1 sobre de regalo"
            : "Has recibido {$this->packsCount} sobres de regalo";

        return [
            'type' => 'packs_gifted',
            'packs_count' => $this->packsCount,
            'message' => $this->message ?? $defaultMessage,
            'custom_message' => $this->message,
        ];
    }
}
