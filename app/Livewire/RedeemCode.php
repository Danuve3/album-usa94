<?php

namespace App\Livewire;

use App\Services\RedeemCodeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RedeemCode extends Component
{
    public string $code = '';

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public function redeem(RedeemCodeService $service): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $code = trim($this->code);

        if ($code === '') {
            $this->errorMessage = 'Introduce un código para canjear.';
            $this->dispatch('code-error');

            return;
        }

        $result = $service->redeem($code, Auth::user());

        if ($result['success']) {
            $this->successMessage = $result['message'];
            $this->code = '';
            $this->dispatch('packs-delivered');
            $this->dispatch('code-redeemed');
        } else {
            $this->errorMessage = $result['message'];
            $this->dispatch('code-error');
        }
    }

    public function render(): View
    {
        return view('livewire.redeem-code');
    }
}
