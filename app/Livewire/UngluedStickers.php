<?php

namespace App\Livewire;

use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UngluedStickers extends Component
{
    public string $search = '';

    /**
     * @var array<int, array{id: int, user_sticker_id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, count: int}>
     */
    public array $stickers = [];

    public int $totalCount = 0;

    public function mount(): void
    {
        $this->loadStickers();
    }

    #[On('sticker-glued')]
    #[On('pack-opened')]
    public function refresh(): void
    {
        $this->loadStickers();
    }

    public function loadStickers(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->stickers = [];
            $this->totalCount = 0;

            return;
        }

        $ungluedStickers = UserSticker::where('user_id', $user->id)
            ->where('is_glued', false)
            ->with('sticker')
            ->get();

        $grouped = $ungluedStickers->groupBy('sticker_id');

        $this->stickers = $grouped->map(function ($group) {
            $firstUserSticker = $group->first();
            $sticker = $firstUserSticker->sticker;

            return [
                'id' => $sticker->id,
                'user_sticker_id' => $firstUserSticker->id,
                'number' => $sticker->number,
                'name' => $sticker->name,
                'page_number' => $sticker->page_number,
                'image_path' => $sticker->image_path,
                'rarity' => $sticker->rarity->value,
                'is_horizontal' => $sticker->is_horizontal,
                'count' => $group->count(),
            ];
        })
            ->sortBy('number')
            ->values()
            ->toArray();

        $this->totalCount = $ungluedStickers->count();
    }

    /**
     * @return array<int, array{id: int, user_sticker_id: int, number: int, name: string, page_number: int, image_path: string|null, rarity: string, count: int}>
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
        return view('livewire.unglued-stickers', [
            'filteredStickers' => $this->filteredStickers,
        ]);
    }
}
