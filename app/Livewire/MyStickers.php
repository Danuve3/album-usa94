<?php

namespace App\Livewire;

use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyStickers extends Component
{
    public string $search = '';

    public string $filter = 'all'; // all, glued, unglued, duplicates

    /**
     * @var array<int, array{id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, total_count: int, glued_count: int, unglued_count: int, is_glued: bool}>
     */
    public array $stickers = [];

    public int $totalStickers = 0;

    public int $uniqueStickers = 0;

    public function mount(): void
    {
        $this->loadStickers();
    }

    public function loadStickers(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->stickers = [];
            $this->totalStickers = 0;
            $this->uniqueStickers = 0;

            return;
        }

        $allUserStickers = UserSticker::where('user_id', $user->id)
            ->with('sticker')
            ->get();

        $grouped = $allUserStickers->groupBy('sticker_id');

        $this->stickers = $grouped->map(function ($group) {
            $sticker = $group->first()->sticker;
            $totalCount = $group->count();
            $gluedCount = $group->where('is_glued', true)->count();
            $ungluedCount = $group->where('is_glued', false)->count();

            return [
                'id' => $sticker->id,
                'number' => $sticker->number,
                'name' => $sticker->name,
                'page_number' => $sticker->page_number,
                'image_path' => $sticker->image_path,
                'rarity' => $sticker->rarity->value,
                'total_count' => $totalCount,
                'glued_count' => $gluedCount,
                'unglued_count' => $ungluedCount,
                'is_glued' => $gluedCount > 0,
            ];
        })
            ->sortBy('number')
            ->values()
            ->toArray();

        $this->totalStickers = $allUserStickers->count();
        $this->uniqueStickers = count($this->stickers);
    }

    /**
     * @return array<int, array{id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, total_count: int, glued_count: int, unglued_count: int, is_glued: bool}>
     */
    public function getFilteredStickersProperty(): array
    {
        $stickers = $this->stickers;

        // Apply status filter
        if ($this->filter === 'glued') {
            $stickers = array_filter($stickers, fn ($s) => $s['is_glued']);
        } elseif ($this->filter === 'unglued') {
            $stickers = array_filter($stickers, fn ($s) => $s['unglued_count'] > 0);
        } elseif ($this->filter === 'duplicates') {
            $stickers = array_filter($stickers, fn ($s) => $s['total_count'] > 1);
        }

        // Apply search filter
        if (! empty($this->search)) {
            $search = strtolower(trim($this->search));
            $stickers = array_filter($stickers, function ($sticker) use ($search) {
                return str_contains(strtolower($sticker['name']), $search)
                    || str_contains((string) $sticker['number'], $search);
            });
        }

        return array_values($stickers);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function render(): View
    {
        return view('livewire.my-stickers', [
            'filteredStickers' => $this->filteredStickers,
        ]);
    }
}
