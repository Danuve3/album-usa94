<?php

namespace App\Livewire;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Album extends Component
{
    public int $currentPage = 0;

    public int $totalPages = 0;

    /**
     * @var array<int, array{number: int, image_path: string|null}>
     */
    public array $pages = [];

    public function mount(): void
    {
        $this->loadPages();
    }

    public function loadPages(): void
    {
        $pagesCollection = Page::ordered()->get();

        $this->pages = $pagesCollection->map(fn (Page $page) => [
            'number' => $page->number,
            'image_path' => $page->image_path,
        ])->toArray();

        $this->totalPages = count($this->pages);
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
