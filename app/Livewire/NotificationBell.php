<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $showDropdown = false;

    /**
     * Toggle the dropdown visibility.
     */
    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    /**
     * Close the dropdown.
     */
    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCountProperty(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    /**
     * Get recent notifications (last 10).
     *
     * @return Collection<int, DatabaseNotification>
     */
    public function getNotificationsProperty(): Collection
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->take(10)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.notification-bell', [
            'unreadCount' => $this->unreadCount,
            'notifications' => $this->notifications,
        ]);
    }
}
