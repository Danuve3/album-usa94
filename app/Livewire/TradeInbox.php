<?php

namespace App\Livewire;

use App\Enums\TradeStatus;
use App\Models\Trade;
use App\Notifications\TradeRejectedNotification;
use App\Services\TradeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TradeInbox extends Component
{
    public string $activeTab = 'received';

    public ?int $selectedTradeId = null;

    public ?Trade $selectedTrade = null;

    public bool $showConfirmModal = false;

    public string $confirmAction = '';

    public function mount(): void
    {
        $this->activeTab = 'received';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->selectedTradeId = null;
        $this->selectedTrade = null;
    }

    public function selectTrade(int $tradeId): void
    {
        $this->selectedTradeId = $tradeId;
        $this->selectedTrade = Trade::with([
            'sender',
            'receiver',
            'offeredItems.userSticker.sticker',
            'requestedItems.userSticker.sticker',
        ])->find($tradeId);
    }

    public function closeDetail(): void
    {
        $this->selectedTradeId = null;
        $this->selectedTrade = null;
    }

    public function confirmAction(string $action): void
    {
        $this->confirmAction = $action;
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmAction = '';
    }

    public function executeAction(): void
    {
        if (! $this->selectedTrade) {
            return;
        }

        $userId = Auth::id();

        match ($this->confirmAction) {
            'accept' => $this->acceptTrade($userId),
            'reject' => $this->rejectTrade($userId),
            'cancel' => $this->cancelTrade($userId),
            default => null,
        };

        $this->showConfirmModal = false;
        $this->confirmAction = '';
    }

    private function acceptTrade(int $userId): void
    {
        if ($this->selectedTrade->receiver_id !== $userId) {
            session()->flash('error', 'No puedes aceptar este intercambio.');

            return;
        }

        try {
            app(TradeService::class)->execute($this->selectedTrade);
            session()->flash('success', 'Intercambio aceptado. Los cromos han sido transferidos.');
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->closeDetail();
    }

    private function rejectTrade(int $userId): void
    {
        if ($this->selectedTrade->receiver_id !== $userId) {
            session()->flash('error', 'No puedes rechazar este intercambio.');

            return;
        }

        if (! $this->selectedTrade->isPending()) {
            session()->flash('error', 'Este intercambio ya no está pendiente.');

            return;
        }

        $this->selectedTrade->transitionTo(TradeStatus::Rejected);

        $this->selectedTrade->load(['sender', 'receiver', 'offeredItems', 'requestedItems']);
        $this->selectedTrade->sender->notify(new TradeRejectedNotification($this->selectedTrade));

        session()->flash('success', 'Intercambio rechazado.');
        $this->closeDetail();
    }

    private function cancelTrade(int $userId): void
    {
        if ($this->selectedTrade->sender_id !== $userId) {
            session()->flash('error', 'No puedes cancelar este intercambio.');

            return;
        }

        if (! $this->selectedTrade->isPending()) {
            session()->flash('error', 'Este intercambio ya no está pendiente.');

            return;
        }

        $this->selectedTrade->transitionTo(TradeStatus::Cancelled);

        session()->flash('success', 'Propuesta cancelada.');
        $this->closeDetail();
    }

    /**
     * Get trades received by the current user.
     *
     * @return Collection<int, Trade>
     */
    public function getReceivedTradesProperty(): Collection
    {
        return Trade::with(['sender', 'offeredItems', 'requestedItems'])
            ->where('receiver_id', Auth::id())
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get trades sent by the current user.
     *
     * @return Collection<int, Trade>
     */
    public function getSentTradesProperty(): Collection
    {
        return Trade::with(['receiver', 'offeredItems', 'requestedItems'])
            ->where('sender_id', Auth::id())
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get count of pending received trades.
     */
    public function getPendingReceivedCountProperty(): int
    {
        return Trade::where('receiver_id', Auth::id())
            ->pending()
            ->active()
            ->count();
    }

    /**
     * Get count of pending sent trades.
     */
    public function getPendingSentCountProperty(): int
    {
        return Trade::where('sender_id', Auth::id())
            ->pending()
            ->active()
            ->count();
    }

    public function render(): View
    {
        return view('livewire.trade-inbox', [
            'receivedTrades' => $this->receivedTrades,
            'sentTrades' => $this->sentTrades,
            'pendingReceivedCount' => $this->pendingReceivedCount,
            'pendingSentCount' => $this->pendingSentCount,
        ]);
    }
}
