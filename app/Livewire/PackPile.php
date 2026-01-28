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
                'image_path' => $userSticker->sticker->image_path,
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
