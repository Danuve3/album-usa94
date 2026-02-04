<?php

namespace App\Livewire;

use App\Models\Pack;
use App\Models\Setting;
use App\Services\PackDeliveryService;
use App\Services\PackService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PackPile extends Component
{
    public int $unopenedCount = 0;

    public bool $isOpening = false;

    public array $lastOpenedStickers = [];

    public bool $showRevealModal = false;

    public bool $showRipAnimation = false;

    public int $revealedCount = 0;

    public int $secondsUntilNextPack = 0;

    public function mount(PackDeliveryService $packDeliveryService): void
    {
        $this->deliverPendingPacksAndRefresh($packDeliveryService);
    }

    public function refreshCount(PackDeliveryService $packDeliveryService): void
    {
        $this->deliverPendingPacksAndRefresh($packDeliveryService);
    }

    private function deliverPendingPacksAndRefresh(PackDeliveryService $packDeliveryService): void
    {
        $user = Auth::user();

        // Deliver any pending packs before counting
        $delivered = $packDeliveryService->deliverPendingPacks($user);

        // Refresh user to get updated last_pack_received_at
        $user->refresh();

        $this->unopenedCount = Pack::where('user_id', $user->id)
            ->unopened()
            ->count();

        $this->secondsUntilNextPack = $packDeliveryService->getSecondsUntilNextPack($user);

        // Notify other components if packs were delivered
        if ($delivered > 0) {
            $this->dispatch('packs-delivered');
        }
    }

    public function revealNextSticker(): void
    {
        if ($this->revealedCount < count($this->lastOpenedStickers)) {
            $this->revealedCount++;
        }
    }

    public function revealAllStickers(): void
    {
        $this->revealedCount = count($this->lastOpenedStickers);
    }

    public function finishReveal(): void
    {
        $this->showRevealModal = false;
        $this->lastOpenedStickers = [];
        $this->revealedCount = 0;
    }

    public function openPack(PackService $packService, PackDeliveryService $packDeliveryService): void
    {
        $pack = Pack::where('user_id', Auth::id())
            ->unopened()
            ->oldest()
            ->first();

        if (! $pack) {
            return;
        }

        $this->isOpening = true;

        $userStickers = $packService->open($pack);

        $this->lastOpenedStickers = $userStickers->map(function ($userSticker) {
            return [
                'id' => $userSticker->id,
                'number' => $userSticker->sticker->number,
                'name' => $userSticker->sticker->name,
                'rarity' => $userSticker->sticker->rarity->value,
                'image_path' => $userSticker->sticker->image_path,
                'is_duplicate' => $userSticker->is_duplicate ?? false,
            ];
        })->toArray();

        $this->refreshCount($packDeliveryService);
        $this->isOpening = false;
        $this->showRipAnimation = true;
        $this->revealedCount = 0;

        $this->dispatch('pack-opened');
    }

    public function finishRipAnimation(): void
    {
        $this->showRipAnimation = false;
        $this->showRevealModal = true;
    }

    public function clearLastOpened(): void
    {
        $this->lastOpenedStickers = [];
        $this->showRevealModal = false;
        $this->revealedCount = 0;
    }

    public function render(): View
    {
        return view('livewire.pack-pile', [
            'normalStyleEnabled' => Setting::get('sticker_style_normal_enabled', true),
            'shinyStyleEnabled' => Setting::get('sticker_style_shiny_enabled', true),
        ]);
    }
}
