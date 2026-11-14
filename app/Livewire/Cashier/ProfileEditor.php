<?php

namespace App\Livewire\Cashier;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileEditor extends Component
{
    public bool $showModal = false;

    public bool $showTrigger = true;

    public string $name = '';

    public string $store_name = '';

    public ?string $bank_name = '';

    public ?string $account_number = '';

    public ?string $account_owner_name = '';

    public bool $isMetaConnected = false;

    /**
     * Mount the component.
     */
    public function mount(bool $showTrigger = true): void
    {
        $this->showTrigger = $showTrigger;
        $this->loadData();
    }

    /**
     * Load current user and store profile data.
     */
    public function loadData(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $this->name = $user->name ?? '';

        $profile = $user->storeProfile;

        if ($profile) {
            $this->store_name = $profile->store_name ?? '';
            $this->bank_name = $profile->bank_name ?? '';
            $this->account_number = $profile->account_number ?? '';
            $this->account_owner_name = $profile->account_owner_name ?? '';
            $this->isMetaConnected = ! empty($profile->meta_phone_number_id) && ! empty($profile->meta_access_token);
        } else {
            $this->store_name = $user->name ? $user->name."'s Store" : '';
            $this->bank_name = '';
            $this->account_number = '';
            $this->account_owner_name = '';
            $this->isMetaConnected = false;
        }
    }

    /**
     * Open the profile editor pop-up.
     */
    #[On('open-profile-editor')]
    public function open(): void
    {
        $this->loadData();
        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Close the profile editor pop-up.
     */
    public function close(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
    }

    /**
     * Quick select bank name.
     */
    public function selectBank(string $bank): void
    {
        $this->bank_name = $bank;
    }

    /**
     * Save the updated profile and bank account details.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'store_name' => ['required', 'string', 'min:2', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'account_owner_name' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'store_name.required' => 'Nama toko / kedai wajib diisi.',
        ]);

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        // Update User Name
        if ($user->name !== $validated['name']) {
            $user->name = trim($validated['name']);
            $user->save();
        }

        // Update or Create Store Profile Details
        $user->storeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => trim($validated['store_name']),
                'bank_name' => $validated['bank_name'] ? trim($validated['bank_name']) : null,
                'account_number' => $validated['account_number'] ? trim($validated['account_number']) : null,
                'account_owner_name' => $validated['account_owner_name'] ? trim($validated['account_owner_name']) : null,
            ]
        );

        $this->loadData();

        Flux::toast(variant: 'success', text: __('Profil dan rekening bank berhasil diperbarui.'));

        $this->dispatch('profile-updated');

        $this->showModal = false;
    }

    /**
     * Render the Livewire component.
     */
    public function render(): View
    {
        return view('livewire.cashier.profile-editor');
    }
}
