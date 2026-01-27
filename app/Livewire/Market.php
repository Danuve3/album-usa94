<?php

namespace App\Livewire;

use App\Enums\MarketListingStatus;
use App\Enums\TradeItemDirection;
use App\Enums\TradeStatus;
use App\Models\MarketListing;
use App\Models\Sticker;
use App\Models\Trade;
use App\Models\UserSticker;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Market extends Component
{
    public string $searchTerm = '';

    public string $filterType = 'all';

    public ?int $selectedStickerId = null;

    public bool $showPublishModal = false;

    public ?int $publishStickerId = null;

    public ?int $wantedStickerId = null;

    public bool $showTradeModal = false;

    public ?MarketListing $selectedListing = null;

    /** @var array<int> */
    public array $offeredStickerIds = [];

    public bool $showSuccessModal = false;

    public string $successMessage = '';

    /**
     * @var Collection<int, Sticker>
     */
    public Collection $searchResults;

    public function mount(): void
    {
        $this->searchResults = collect();
    }

    public function updatedSearchTerm(): void
    {
        if (strlen($this->searchTerm) < 1) {
            $this->searchResults = collect();
            $this->selectedStickerId = null;

            return;
        }

        $this->searchResults = Sticker::where('number', 'like', '%'.$this->searchTerm.'%')
            ->orWhere('name', 'like', '%'.$this->searchTerm.'%')
            ->orderBy('number')
            ->limit(20)
            ->get();
    }

    public function selectSticker(int $stickerId): void
    {
        $this->selectedStickerId = $stickerId;
        $this->searchTerm = '';
        $this->searchResults = collect();
    }

    public function clearFilter(): void
    {
        $this->selectedStickerId = null;
        $this->filterType = 'all';
    }

    public function openPublishModal(int $stickerId): void
    {
        $this->publishStickerId = $stickerId;
        $this->wantedStickerId = null;
        $this->showPublishModal = true;
    }

    public function closePublishModal(): void
    {
        $this->showPublishModal = false;
        $this->publishStickerId = null;
        $this->wantedStickerId = null;
    }

    public function publishListing(): void
    {
        if (! $this->publishStickerId) {
            return;
        }

        $user = Auth::user();

        $userSticker = UserSticker::where('user_id', $user->id)
            ->where('sticker_id', $this->publishStickerId)
            ->where('is_glued', false)
            ->first();

        if (! $userSticker) {
            session()->flash('error', 'No tienes ese cromo disponible para publicar.');
            $this->closePublishModal();

            return;
        }

        $existingListing = MarketListing::where('user_sticker_id', $userSticker->id)
            ->active()
            ->first();

        if ($existingListing) {
            session()->flash('error', 'Ya tienes este cromo publicado en el mercado.');
            $this->closePublishModal();

            return;
        }

        MarketListing::create([
            'user_id' => $user->id,
            'user_sticker_id' => $userSticker->id,
            'wanted_sticker_id' => $this->wantedStickerId,
            'status' => MarketListingStatus::Active,
            'expires_at' => now()->addDays(14),
        ]);

        $this->successMessage = 'Cromo publicado en el mercado correctamente.';
        $this->showSuccessModal = true;
        $this->closePublishModal();
    }

    public function cancelListing(int $listingId): void
    {
        $listing = MarketListing::where('id', $listingId)
            ->where('user_id', Auth::id())
            ->first();

        if ($listing && $listing->isActive()) {
            $listing->transitionTo(MarketListingStatus::Cancelled);
            session()->flash('success', 'Publicación cancelada correctamente.');
        }
    }

    public function openTradeModal(int $listingId): void
    {
        $this->selectedListing = MarketListing::with(['user', 'userSticker.sticker', 'wantedSticker'])
            ->find($listingId);

        if (! $this->selectedListing || ! $this->selectedListing->isActive()) {
            session()->flash('error', 'Esta oferta ya no está disponible.');

            return;
        }

        if ($this->selectedListing->user_id === Auth::id()) {
            session()->flash('error', 'No puedes intercambiar con tu propia oferta.');

            return;
        }

        $this->offeredStickerIds = [];
        $this->showTradeModal = true;
    }

    public function closeTradeModal(): void
    {
        $this->showTradeModal = false;
        $this->selectedListing = null;
        $this->offeredStickerIds = [];
    }

    public function toggleOfferedSticker(int $stickerId): void
    {
        if (in_array($stickerId, $this->offeredStickerIds)) {
            $this->offeredStickerIds = array_values(array_diff($this->offeredStickerIds, [$stickerId]));
        } else {
            $this->offeredStickerIds[] = $stickerId;
        }
    }

    public function initiateTradeFromListing(): void
    {
        if (! $this->selectedListing || empty($this->offeredStickerIds)) {
            return;
        }

        $currentUser = Auth::user();
        $listing = $this->selectedListing;

        if (! $listing->isActive()) {
            session()->flash('error', 'Esta oferta ya no está disponible.');
            $this->closeTradeModal();

            return;
        }

        $offeredUserStickers = UserSticker::where('user_id', $currentUser->id)
            ->whereIn('sticker_id', $this->offeredStickerIds)
            ->where('is_glued', false)
            ->get()
            ->groupBy('sticker_id')
            ->map(fn ($group) => $group->first());

        if ($offeredUserStickers->count() !== count($this->offeredStickerIds)) {
            session()->flash('error', 'Algunos cromos seleccionados ya no están disponibles.');
            $this->closeTradeModal();

            return;
        }

        DB::transaction(function () use ($currentUser, $listing, $offeredUserStickers) {
            $trade = Trade::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $listing->user_id,
                'status' => TradeStatus::Pending,
                'expires_at' => now()->addDays(7),
            ]);

            foreach ($offeredUserStickers as $userSticker) {
                $trade->items()->create([
                    'user_sticker_id' => $userSticker->id,
                    'direction' => TradeItemDirection::Offered,
                ]);
            }

            $trade->items()->create([
                'user_sticker_id' => $listing->user_sticker_id,
                'direction' => TradeItemDirection::Requested,
            ]);
        });

        $this->successMessage = 'Propuesta de intercambio enviada correctamente.';
        $this->showSuccessModal = true;
        $this->closeTradeModal();
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    /**
     * Get current user's duplicate stickers available for publishing.
     *
     * @return array<int, array{sticker_id: int, number: int, name: string, image_path: string|null, rarity: string, count: int}>
     */
    public function getMyDuplicatesProperty(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $userStickers = UserSticker::where('user_id', $user->id)
            ->where('is_glued', false)
            ->with('sticker')
            ->get();

        $grouped = $userStickers->groupBy('sticker_id');

        return $grouped->map(function ($group) {
            $sticker = $group->first()->sticker;

            return [
                'sticker_id' => $sticker->id,
                'number' => $sticker->number,
                'name' => $sticker->name,
                'image_path' => $sticker->image_path,
                'rarity' => $sticker->rarity->value,
                'count' => $group->count(),
            ];
        })
            ->sortBy('number')
            ->values()
            ->toArray();
    }

    /**
     * Get stickers that the current user needs (doesn't have).
     *
     * @return Collection<int, Sticker>
     */
    public function getNeededStickersProperty(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        $ownedStickerIds = UserSticker::where('user_id', $user->id)
            ->pluck('sticker_id')
            ->unique();

        return Sticker::whereNotIn('id', $ownedStickerIds)
            ->orderBy('number')
            ->get();
    }

    /**
     * Get my active listings.
     *
     * @return Collection<int, MarketListing>
     */
    public function getMyListingsProperty(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        return MarketListing::where('user_id', $user->id)
            ->active()
            ->with(['userSticker.sticker', 'wantedSticker'])
            ->get();
    }

    /**
     * Get listings offering stickers that I need.
     *
     * @return Collection<int, MarketListing>
     */
    public function getListingsIWantProperty(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        $neededStickerIds = $this->neededStickers->pluck('id');

        return MarketListing::active()
            ->excludeUser($user->id)
            ->whereHas('userSticker', function ($query) use ($neededStickerIds) {
                $query->whereIn('sticker_id', $neededStickerIds);
            })
            ->with(['user', 'userSticker.sticker', 'wantedSticker'])
            ->get();
    }

    /**
     * Get all market listings based on current filters.
     *
     * @return Collection<int, MarketListing>
     */
    public function getListingsProperty(): Collection
    {
        $user = Auth::user();
        $query = MarketListing::active()
            ->with(['user', 'userSticker.sticker', 'wantedSticker']);

        if ($user) {
            $query->excludeUser($user->id);
        }

        if ($this->selectedStickerId) {
            if ($this->filterType === 'offering') {
                $query->offering($this->selectedStickerId);
            } elseif ($this->filterType === 'wanting') {
                $query->wanting($this->selectedStickerId);
            } else {
                $query->where(function ($q) {
                    $q->offering($this->selectedStickerId)
                        ->orWhere('wanted_sticker_id', $this->selectedStickerId);
                });
            }
        }

        return $query->latest()->get();
    }

    public function render(): View
    {
        return view('livewire.market', [
            'listings' => $this->listings,
            'myDuplicates' => $this->myDuplicates,
            'neededStickers' => $this->neededStickers,
            'myListings' => $this->myListings,
            'listingsIWant' => $this->listingsIWant,
        ]);
    }
}
