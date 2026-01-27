<?php

namespace App\Livewire;

use App\Enums\TradeItemDirection;
use App\Enums\TradeStatus;
use App\Models\Trade;
use App\Models\User;
use App\Models\UserSticker;
use App\Notifications\TradeProposalNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TradeProposal extends Component
{
    public string $userSearch = '';

    public ?int $selectedUserId = null;

    public ?User $selectedUser = null;

    /** @var array<int> */
    public array $offeredStickerIds = [];

    /** @var array<int> */
    public array $requestedStickerIds = [];

    public bool $showSuccessModal = false;

    public ?int $createdTradeId = null;

    /**
     * @var Collection<int, User>
     */
    public Collection $searchResults;

    public function mount(): void
    {
        $this->searchResults = collect();
    }

    public function updatedUserSearch(): void
    {
        if (strlen($this->userSearch) < 2) {
            $this->searchResults = collect();

            return;
        }

        $currentUserId = Auth::id();

        $this->searchResults = User::where('id', '!=', $currentUserId)
            ->where('is_banned', false)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->userSearch.'%')
                    ->orWhere('email', 'like', '%'.$this->userSearch.'%');
            })
            ->limit(10)
            ->get();
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->selectedUser = User::find($userId);
        $this->userSearch = '';
        $this->searchResults = collect();
        $this->offeredStickerIds = [];
        $this->requestedStickerIds = [];
    }

    public function clearSelectedUser(): void
    {
        $this->selectedUserId = null;
        $this->selectedUser = null;
        $this->offeredStickerIds = [];
        $this->requestedStickerIds = [];
    }

    public function toggleOfferedSticker(int $stickerId): void
    {
        if (in_array($stickerId, $this->offeredStickerIds)) {
            $this->offeredStickerIds = array_values(array_diff($this->offeredStickerIds, [$stickerId]));
        } else {
            $this->offeredStickerIds[] = $stickerId;
        }
    }

    public function toggleRequestedSticker(int $stickerId): void
    {
        if (in_array($stickerId, $this->requestedStickerIds)) {
            $this->requestedStickerIds = array_values(array_diff($this->requestedStickerIds, [$stickerId]));
        } else {
            $this->requestedStickerIds[] = $stickerId;
        }
    }

    public function sendProposal(): void
    {
        if (! $this->selectedUserId || empty($this->offeredStickerIds) || empty($this->requestedStickerIds)) {
            return;
        }

        $currentUser = Auth::user();

        // Validate offered stickers belong to current user and are duplicates (unglued)
        $offeredUserStickers = UserSticker::where('user_id', $currentUser->id)
            ->whereIn('sticker_id', $this->offeredStickerIds)
            ->where('is_glued', false)
            ->get()
            ->groupBy('sticker_id')
            ->map(fn ($group) => $group->first());

        if ($offeredUserStickers->count() !== count($this->offeredStickerIds)) {
            session()->flash('error', 'Algunos cromos seleccionados ya no están disponibles.');

            return;
        }

        // Validate requested stickers belong to selected user and are duplicates (unglued)
        $requestedUserStickers = UserSticker::where('user_id', $this->selectedUserId)
            ->whereIn('sticker_id', $this->requestedStickerIds)
            ->where('is_glued', false)
            ->get()
            ->groupBy('sticker_id')
            ->map(fn ($group) => $group->first());

        if ($requestedUserStickers->count() !== count($this->requestedStickerIds)) {
            session()->flash('error', 'Algunos cromos del otro usuario ya no están disponibles.');

            return;
        }

        DB::transaction(function () use ($currentUser, $offeredUserStickers, $requestedUserStickers) {
            $trade = Trade::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $this->selectedUserId,
                'status' => TradeStatus::Pending,
                'expires_at' => now()->addDays(7),
            ]);

            // Add offered items
            foreach ($offeredUserStickers as $userSticker) {
                $trade->items()->create([
                    'user_sticker_id' => $userSticker->id,
                    'direction' => TradeItemDirection::Offered,
                ]);
            }

            // Add requested items
            foreach ($requestedUserStickers as $userSticker) {
                $trade->items()->create([
                    'user_sticker_id' => $userSticker->id,
                    'direction' => TradeItemDirection::Requested,
                ]);
            }

            $this->createdTradeId = $trade->id;

            $trade->load(['sender', 'offeredItems', 'requestedItems']);
            $trade->receiver->notify(new TradeProposalNotification($trade));
        });

        $this->showSuccessModal = true;
        $this->offeredStickerIds = [];
        $this->requestedStickerIds = [];
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->createdTradeId = null;
        $this->clearSelectedUser();
    }

    /**
     * Get current user's duplicate stickers available for trade.
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
     * Get selected user's duplicate stickers available for trade.
     *
     * @return array<int, array{sticker_id: int, number: int, name: string, image_path: string|null, rarity: string, count: int}>
     */
    public function getTheirDuplicatesProperty(): array
    {
        if (! $this->selectedUserId) {
            return [];
        }

        $userStickers = UserSticker::where('user_id', $this->selectedUserId)
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

    public function render(): View
    {
        return view('livewire.trade-proposal', [
            'myDuplicates' => $this->myDuplicates,
            'theirDuplicates' => $this->theirDuplicates,
        ]);
    }
}
