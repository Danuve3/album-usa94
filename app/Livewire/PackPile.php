<?php

namespace App\Livewire;

use App\Models\Pack;
use App\Services\PackService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PackPile extends Component
{
    public int $unopenedCount = 0;

    public bool $isOpening = false;

    public bool $showOpeningModal = false;

    public bool $isTearing = false;

    public array $lastOpenedStickers = [];

    public bool $showRevealModal = false;

    public int $revealedCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $this->unopenedCount = Pack::where('user_id', Auth::id())
            ->unopened()
            ->count();
    }

    public function startOpening(): void
    {
        if ($this->unopenedCount === 0 || $this->isOpening) {
            return;
        }

        $this->showOpeningModal = true;
        $this->isOpening = true;
    }

    public function cancelOpening(): void
    {
        if ($this->isTearing) {
            return;
        }

        $this->showOpeningModal = false;
        $this->isOpening = false;
    }

    public function tearPack(PackService $packService): void
    {
        $pack = Pack::where('user_id', Auth::id())
            ->unopened()
            ->oldest()
            ->first();

        if (! $pack) {
            $this->showOpeningModal = false;
            $this->isOpening = false;

            return;
        }

        $this->isTearing = true;

        $userStickers = $packService->open($pack);

        $this->lastOpenedStickers = $userStickers->map(function ($userSticker) {
            return [
                'id' => $userSticker->id,
                'number' => $userSticker->sticker->number,
                'name' => $userSticker->sticker->name,
                'rarity' => $userSticker->sticker->rarity->value,
                'is_duplicate' => $userSticker->is_duplicate ?? false,
            ];
        })->toArray();

        $this->refreshCount();

        $this->dispatch('pack-opened');
    }

    public function finishOpening(): void
    {
        $this->showOpeningModal = false;
        $this->isOpening = false;
        $this->isTearing = false;
        $this->showRevealModal = true;
        $this->revealedCount = 0;
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

    public function openPack(PackService $packService): void
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
                'is_duplicate' => $userSticker->is_duplicate ?? false,
            ];
        })->toArray();

        $this->refreshCount();
        $this->isOpening = false;
        $this->showRevealModal = true;
        $this->revealedCount = 0;

        $this->dispatch('pack-opened');
    }

    public function clearLastOpened(): void
    {
        $this->lastOpenedStickers = [];
        $this->showRevealModal = false;
        $this->revealedCount = 0;
    }

    public function render(): View
    {
        return view('livewire.pack-pile');
    }
}
