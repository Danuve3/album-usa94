<?php

namespace App\Livewire;

use App\Models\Pack;
use App\Services\PackDeliveryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PackCounter extends Component
{
    public int $unopenedCount = 0;

    public int $secondsUntilNextPack = 0;

    public function mount(PackDeliveryService $packDeliveryService): void
    {
        $user = Auth::user();

        // Only read data, don't deliver packs here (PackPile handles delivery)
        $user->refresh();

        $this->unopenedCount = Pack::where('user_id', $user->id)
            ->unopened()
            ->count();

        $this->secondsUntilNextPack = $packDeliveryService->getSecondsUntilNextPack($user);
    }

    #[On('pack-opened')]
    #[On('packs-delivered')]
    public function refresh(PackDeliveryService $packDeliveryService): void
    {
        $user = Auth::user();

        // Deliver any pending packs (database locking prevents duplicates)
        $packDeliveryService->deliverPendingPacks($user);

        // Refresh user to get updated last_pack_received_at
        $user->refresh();

        $this->unopenedCount = Pack::where('user_id', $user->id)
            ->unopened()
            ->count();

        $this->secondsUntilNextPack = $packDeliveryService->getSecondsUntilNextPack($user);
    }

    public function render(): View
    {
        return view('livewire.pack-counter');
    }
}
