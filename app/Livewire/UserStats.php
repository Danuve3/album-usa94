<?php

namespace App\Livewire;

use App\Enums\StickerRarity;
use App\Models\Page;
use App\Models\Sticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserStats extends Component
{
    public int $totalStickers = 0;

    public int $gluedStickers = 0;

    public int $completionPercentage = 0;

    public int $totalShiny = 0;

    public int $ownedShiny = 0;

    public int $totalPacksOpened = 0;

    /**
     * @var array<int, array{number: int, name: string, glued: int, total: int, percentage: int}>
     */
    public array $statsByPage = [];

    /**
     * @var array<int, array{date: string, packs: int}>
     */
    public array $packsHistory = [];

    /**
     * @var array<int, array{date: string, stickers: int}>
     */
    public array $progressHistory = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->loadAlbumCompletion($user);
        $this->loadShinyStats($user);
        $this->loadStatsByPage($user);
        $this->loadPacksHistory($user);
        $this->loadProgressHistory($user);
    }

    private function loadAlbumCompletion($user): void
    {
        $this->totalStickers = Sticker::count();
        $this->gluedStickers = $user->userStickers()->glued()->distinct('sticker_id')->count('sticker_id');
        $this->completionPercentage = $this->totalStickers > 0
            ? (int) round(($this->gluedStickers / $this->totalStickers) * 100)
            : 0;
    }

    private function loadShinyStats($user): void
    {
        $this->totalShiny = Sticker::where('rarity', StickerRarity::Shiny)->count();

        $shinyIds = Sticker::where('rarity', StickerRarity::Shiny)->pluck('id');
        $this->ownedShiny = $user->userStickers()
            ->whereIn('sticker_id', $shinyIds)
            ->distinct('sticker_id')
            ->count('sticker_id');
    }

    private function loadStatsByPage($user): void
    {
        $pages = Page::ordered()->get();

        $userGluedByPage = $user->userStickers()
            ->glued()
            ->join('stickers', 'user_stickers.sticker_id', '=', 'stickers.id')
            ->select('stickers.page_number', DB::raw('COUNT(DISTINCT user_stickers.sticker_id) as count'))
            ->groupBy('stickers.page_number')
            ->pluck('count', 'page_number');

        $stickersByPage = Sticker::select('page_number', DB::raw('COUNT(*) as count'))
            ->groupBy('page_number')
            ->pluck('count', 'page_number');

        $this->statsByPage = $pages->map(function (Page $page) use ($userGluedByPage, $stickersByPage) {
            $total = $stickersByPage[$page->number] ?? 0;
            $glued = $userGluedByPage[$page->number] ?? 0;
            $percentage = $total > 0 ? (int) round(($glued / $total) * 100) : 0;

            return [
                'number' => $page->number,
                'name' => "Página {$page->number}",
                'glued' => $glued,
                'total' => $total,
                'percentage' => $percentage,
            ];
        })->toArray();
    }

    private function loadPacksHistory($user): void
    {
        $this->totalPacksOpened = $user->packs()->opened()->count();

        $history = $user->packs()
            ->opened()
            ->select(DB::raw('DATE(opened_at) as date'), DB::raw('COUNT(*) as packs'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $this->packsHistory = $history->map(fn ($item) => [
            'date' => $item->date,
            'packs' => $item->packs,
        ])->reverse()->values()->toArray();
    }

    private function loadProgressHistory($user): void
    {
        $history = $user->userStickers()
            ->select(DB::raw('DATE(obtained_at) as date'), DB::raw('COUNT(DISTINCT sticker_id) as stickers'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Build cumulative progress
        $cumulative = [];
        $total = 0;

        $sortedHistory = $history->sortBy('date');
        foreach ($sortedHistory as $item) {
            $total += $item->stickers;
            $cumulative[] = [
                'date' => $item->date,
                'stickers' => $total,
            ];
        }

        $this->progressHistory = $cumulative;
    }

    public function render(): View
    {
        return view('livewire.user-stats');
    }
}
