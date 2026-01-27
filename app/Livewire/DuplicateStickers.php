<?php

namespace App\Livewire;

use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DuplicateStickers extends Component
{
    public string $search = '';

    /**
     * @var array<int, array{id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, count: int, extra_count: int}>
     */
    public array $stickers = [];

    public int $totalDuplicates = 0;

    public function mount(): void
    {
        $this->loadDuplicates();
    }

    public function loadDuplicates(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->stickers = [];
            $this->totalDuplicates = 0;

            return;
        }

        // Get all user stickers (both glued and unglued) grouped by sticker_id
        $allUserStickers = UserSticker::where('user_id', $user->id)
            ->with('sticker')
            ->get();

        $grouped = $allUserStickers->groupBy('sticker_id');

        // Filter only stickers where user has more than 1 copy
        $duplicates = $grouped->filter(function ($group) {
            return $group->count() > 1;
        });

        $this->stickers = $duplicates->map(function ($group) {
            $sticker = $group->first()->sticker;
            $totalCount = $group->count();
            $gluedCount = $group->where('is_glued', true)->count();

            // Extra count = total - 1 (keeping one for the album)
            // If one is glued, extras = total - 1
            // If none is glued, extras = total - 1 (one will be used to glue)
            $extraCount = $totalCount - 1;

            return [
                'id' => $sticker->id,
                'number' => $sticker->number,
                'name' => $sticker->name,
                'page_number' => $sticker->page_number,
                'image_path' => $sticker->image_path,
                'rarity' => $sticker->rarity->value,
                'count' => $totalCount,
                'extra_count' => $extraCount,
                'is_glued' => $gluedCount > 0,
            ];
        })
            ->sortBy('number')
            ->values()
            ->toArray();

        // Total duplicates = sum of all extra copies
        $this->totalDuplicates = array_sum(array_column($this->stickers, 'extra_count'));
    }

    /**
     * @return array<int, array{id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, count: int, extra_count: int}>
     */
    public function getFilteredStickersProperty(): array
    {
        if (empty($this->search)) {
            return $this->stickers;
        }

        $search = strtolower(trim($this->search));

        return array_values(array_filter($this->stickers, function ($sticker) use ($search) {
            return str_contains(strtolower($sticker['name']), $search)
                || str_contains((string) $sticker['number'], $search);
        }));
    }

    public function render(): View
    {
        return view('livewire.duplicate-stickers', [
            'filteredStickers' => $this->filteredStickers,
        ]);
    }
}
