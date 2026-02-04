<?php

namespace App\Livewire;

use App\Models\Page;
use App\Models\Sticker;
use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Album extends Component
{
    public int $currentPage = 0;

    public int $totalPages = 0;

    /**
     * @var array<int, array{number: int, image_path: string|null, stickers: array<int, array{id: int, number: int, name: string, position_x: int, position_y: int, width: int, height: int, is_horizontal: bool, image_path: string|null, status: string}>, glued_count: int, total_count: int}>
     */
    public array $pages = [];

    public function mount(): void
    {
        $this->loadPages();
    }

    public function loadPages(): void
    {
        $pagesCollection = Page::ordered()->get();

        $allStickersWithStatus = $this->getAllStickersWithStatusGroupedByPage();

        // Add cover page
        $pages = [[
            'number' => 0,
            'type' => 'cover',
            'image_path' => 'pages/cover.webp',
            'stickers' => [],
            'glued_count' => 0,
            'total_count' => 0,
        ]];

        // Add content pages
        foreach ($pagesCollection as $page) {
            $pageStickers = $allStickersWithStatus[$page->number] ?? [];
            $gluedCount = collect($pageStickers)->where('status', 'glued')->count();

            $pages[] = [
                'number' => $page->number,
                'type' => 'content',
                'image_path' => $page->image_path,
                'stickers' => $pageStickers,
                'glued_count' => $gluedCount,
                'total_count' => count($pageStickers),
            ];
        }

        // Add back cover page
        $pages[] = [
            'number' => 999,
            'type' => 'back_cover',
            'image_path' => 'pages/back_cover.webp',
            'stickers' => [],
            'glued_count' => 0,
            'total_count' => 0,
        ];

        $this->pages = $pages;
        $this->totalPages = count($this->pages);
    }

    /**
     * Get all stickers with their status for the current user, grouped by page number.
     * Status can be: 'glued' (pegado), 'available' (disponible para pegar), 'empty' (vacío)
     *
     * @return array<int, array<int, array{id: int, number: int, name: string, position_x: int, position_y: int, width: int, height: int, is_horizontal: bool, image_path: string|null, status: string}>>
     */
    private function getAllStickersWithStatusGroupedByPage(): array
    {
        $user = Auth::user();

        // Get all stickers from the catalog
        $allStickers = Sticker::orderBy('number')->get();

        // Get user's sticker statuses if authenticated
        $userGluedStickerIds = [];
        $userAvailableStickerIds = [];

        if ($user) {
            $userStickers = UserSticker::where('user_id', $user->id)
                ->get()
                ->groupBy('sticker_id');

            foreach ($userStickers as $stickerId => $userStickerGroup) {
                $hasGlued = $userStickerGroup->contains('is_glued', true);
                $hasUnglued = $userStickerGroup->contains('is_glued', false);

                if ($hasGlued) {
                    $userGluedStickerIds[] = $stickerId;
                } elseif ($hasUnglued) {
                    $userAvailableStickerIds[] = $stickerId;
                }
            }
        }

        return $allStickers
            ->groupBy('page_number')
            ->map(fn ($stickers) => $stickers->map(function (Sticker $sticker) use ($userGluedStickerIds, $userAvailableStickerIds) {
                $status = 'empty';
                if (in_array($sticker->id, $userGluedStickerIds)) {
                    $status = 'glued';
                } elseif (in_array($sticker->id, $userAvailableStickerIds)) {
                    $status = 'available';
                }

                return [
                    'id' => $sticker->id,
                    'number' => $sticker->number,
                    'name' => $sticker->name,
                    'position_x' => $sticker->position_x,
                    'position_y' => $sticker->position_y,
                    'width' => $sticker->width,
                    'height' => $sticker->height,
                    'is_horizontal' => $sticker->is_horizontal,
                    'image_path' => $sticker->image_path,
                    'status' => $status,
                ];
            })->values()->toArray())
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

    public function glueSticker(int $userStickerId, int $stickerId): array
    {
        $user = Auth::user();

        if (! $user) {
            return ['success' => false, 'message' => 'Usuario no autenticado'];
        }

        $userSticker = UserSticker::where('id', $userStickerId)
            ->where('user_id', $user->id)
            ->where('sticker_id', $stickerId)
            ->where('is_glued', false)
            ->first();

        if (! $userSticker) {
            return ['success' => false, 'message' => 'Cromo no encontrado o ya pegado'];
        }

        $userSticker->is_glued = true;
        $userSticker->save();

        $this->loadPages();

        $this->dispatch('sticker-glued', stickerId: $stickerId);

        return ['success' => true, 'message' => 'Cromo pegado correctamente'];
    }

    public function render(): View
    {
        return view('livewire.album');
    }
}
