<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserSettings extends Component
{
    use WithFileUploads;

    public $avatar;

    public string $name = '';

    public bool $showSuccessMessage = false;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => 'image|max:2048', // 2MB max
        ]);
    }

    public function saveAvatar(): void
    {
        $this->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        // Store new avatar
        $path = $this->avatar->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        $this->avatar = null;
        $this->showSuccessMessage = true;

        $this->dispatch('avatar-updated');
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        $this->showSuccessMessage = true;
    }

    public function saveName(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
        ]);

        Auth::user()->update(['name' => $this->name]);

        $this->showSuccessMessage = true;
    }

    public function render(): View
    {
        return view('livewire.user-settings', [
            'user' => Auth::user(),
        ]);
    }
}
