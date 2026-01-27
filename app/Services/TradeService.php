<?php

namespace App\Services;

use App\Enums\TradeStatus;
use App\Models\Trade;
use App\Models\UserSticker;
use App\Notifications\TradeAcceptedNotification;
use Illuminate\Support\Facades\DB;

class TradeService
{
    /**
     * Execute a trade by transferring stickers between users.
     *
     * @throws \InvalidArgumentException If the trade cannot be executed
     */
    public function execute(Trade $trade): void
    {
        $this->validateTradeCanBeExecuted($trade);

        DB::transaction(function () use ($trade) {
            $offeredItems = $trade->offeredItems()->with('userSticker')->get();
            $requestedItems = $trade->requestedItems()->with('userSticker')->get();

            $this->validateStickersOwnership($trade, $offeredItems, $requestedItems);

            $this->transferStickers($trade, $offeredItems, $requestedItems);

            $trade->transitionTo(TradeStatus::Accepted);

            $this->notifyParties($trade);
        });
    }

    /**
     * Validate that the trade can be executed.
     *
     * @throws \InvalidArgumentException
     */
    private function validateTradeCanBeExecuted(Trade $trade): void
    {
        if (! $trade->isPending()) {
            throw new \InvalidArgumentException('El intercambio ya no está pendiente.');
        }

        if ($trade->isExpired()) {
            throw new \InvalidArgumentException('El intercambio ha expirado.');
        }
    }

    /**
     * Validate that both parties still own their respective stickers.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\TradeItem>  $offeredItems
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\TradeItem>  $requestedItems
     *
     * @throws \InvalidArgumentException
     */
    private function validateStickersOwnership(
        Trade $trade,
        $offeredItems,
        $requestedItems
    ): void {
        foreach ($offeredItems as $item) {
            if (! $item->userSticker) {
                throw new \InvalidArgumentException('Algunos cromos ofrecidos ya no existen.');
            }

            if ($item->userSticker->user_id !== $trade->sender_id) {
                throw new \InvalidArgumentException('El remitente ya no posee algunos cromos ofrecidos.');
            }

            if ($item->userSticker->is_glued) {
                throw new \InvalidArgumentException('Algunos cromos ofrecidos están pegados y no se pueden intercambiar.');
            }
        }

        foreach ($requestedItems as $item) {
            if (! $item->userSticker) {
                throw new \InvalidArgumentException('Algunos cromos solicitados ya no existen.');
            }

            if ($item->userSticker->user_id !== $trade->receiver_id) {
                throw new \InvalidArgumentException('El destinatario ya no posee algunos cromos solicitados.');
            }

            if ($item->userSticker->is_glued) {
                throw new \InvalidArgumentException('Algunos cromos solicitados están pegados y no se pueden intercambiar.');
            }
        }
    }

    /**
     * Transfer stickers between users atomically.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\TradeItem>  $offeredItems
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\TradeItem>  $requestedItems
     */
    private function transferStickers(
        Trade $trade,
        $offeredItems,
        $requestedItems
    ): void {
        foreach ($offeredItems as $item) {
            UserSticker::where('id', $item->user_sticker_id)
                ->update(['user_id' => $trade->receiver_id]);
        }

        foreach ($requestedItems as $item) {
            UserSticker::where('id', $item->user_sticker_id)
                ->update(['user_id' => $trade->sender_id]);
        }
    }

    /**
     * Notify both parties about the completed trade.
     */
    private function notifyParties(Trade $trade): void
    {
        $trade->load(['sender', 'receiver']);

        $trade->sender->notify(new TradeAcceptedNotification($trade, isSender: true));
        $trade->receiver->notify(new TradeAcceptedNotification($trade, isSender: false));
    }
}
