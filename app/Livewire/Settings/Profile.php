<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use App\Models\StoreProfile;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile settings')]
class Profile extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    public string $store_name = '';

    public ?string $bank_name = '';

    public ?string $account_number = '';

    public ?string $account_owner_name = '';

    public bool $is_open = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        $profile = $user->storeProfile;
        if ($profile) {
            $this->store_name = $profile->store_name ?? '';
            $this->bank_name = $profile->bank_name ?? '';
            $this->account_number = $profile->account_number ?? '';
            $this->account_owner_name = $profile->account_owner_name ?? '';
            $this->is_open = (bool) $profile->is_open;
        } else {
            $this->store_name = $user->name ? $user->name."'s Store" : '';
            $this->bank_name = '';
            $this->account_number = '';
            $this->account_owner_name = '';
            $this->is_open = false;
        }
    }

    /**
     * Toggle open/closed status for the store.
     */
    public function toggleStore(): void
    {
        $user = Auth::user();
        $profile = StoreProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['store_name' => $this->store_name ?: ($user->name."'s Store")]
        );

        $this->is_open = ! $this->is_open;
        $profile->update(['is_open' => $this->is_open]);

        Flux::toast(
            text: $this->is_open ? 'Toko dibuka. Selamat berjualan!' : 'Toko ditutup.',
            variant: $this->is_open ? 'success' : 'warning',
        );
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if (config('fortify.lowercase_usernames')) {
            $this->email = Str::lower($this->email);
        }

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update StoreProfile if present or create it
        if (! empty($this->store_name)) {
            $profile = StoreProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['store_name' => $this->store_name]
            );

            $profile->update([
                'store_name' => $this->store_name,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_owner_name' => $this->account_owner_name,
            ]);
        }

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        $user = Auth::user();

        return $user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        $user = Auth::user();

        return ! $user instanceof MustVerifyEmail || $user->hasVerifiedEmail();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.settings.profile', [
            'profile' => Auth::user()?->storeProfile,
        ]);
    }
}
