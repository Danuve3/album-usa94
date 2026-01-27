<?php

namespace App\Livewire;

use App\Models\Page;
use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Album extends Component
{
    public int $currentPage = 0;

    public int $totalPages = 0;

    /**
     * @var array<int, array{number: int, image_path: string|null, stickers: array<int, array{id: int, number: int, name: string, position_x: int, position_y: int, width: int, height: int, is_horizontal: bool, image_path: string|null}>}>
     */
    public array $pages = [];

    public function mount(): void
    {
        $this->loadPages();
    }

    public function loadPages(): void
    {
        $pagesCollection = Page::ordered()->get();

        $gluedStickers = $this->getGluedStickersGroupedByPage();

        $this->pages = $pagesCollection->map(fn (Page $page) => [
            'number' => $page->number,
            'image_path' => $page->image_path,
            'stickers' => $gluedStickers[$page->number] ?? [],
        ])->toArray();

        $this->totalPages = count($this->pages);
    }

    /**
     * Get all glued stickers for the current user, grouped by page number.
     *
     * @return array<int, array<int, array{id: int, number: int, name: string, position_x: int, position_y: int, width: int, height: int, is_horizontal: bool, image_path: string|null}>>
     */
    private function getGluedStickersGroupedByPage(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        return UserSticker::where('user_id', $user->id)
            ->glued()
            ->with('sticker')
            ->get()
            ->groupBy(fn (UserSticker $userSticker) => $userSticker->sticker->page_number)
            ->map(fn ($userStickers) => $userStickers->map(fn (UserSticker $userSticker) => [
                'id' => $userSticker->sticker->id,
                'number' => $userSticker->sticker->number,
                'name' => $userSticker->sticker->name,
                'position_x' => $userSticker->sticker->position_x,
                'position_y' => $userSticker->sticker->position_y,
                'width' => $userSticker->sticker->width,
                'height' => $userSticker->sticker->height,
                'is_horizontal' => $userSticker->sticker->is_horizontal,
                'image_path' => $userSticker->sticker->image_path,
            ])->unique('id')->values()->toArray())
            ->toArray();
    }

    public function pageFlipped(int $page): void
    {
        $this->currentPage = $page;
    }

    public function goToPage(int $page): void
    {
        if ($page >= 0 && $page < $this->totalPages) {
            $this->currentPage = $page;
            $this->dispatch('album-go-to-page', page: $page);
        }
    }

    public function goToFirstPage(): void
    {
        $this->goToPage(0);
    }

    public function goToLastPage(): void
    {
        $this->goToPage($this->totalPages - 1);
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages - 1) {
            $this->goToPage($this->currentPage + 1);
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 0) {
            $this->goToPage($this->currentPage - 1);
        }
    }

    public function render(): View
    {
        return view('livewire.album');
    }
}
