<?php

namespace App\Livewire\Auth;

use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Store Setup - PlingPager')]
class StoreSetup extends Component
{
    public string $store_name = '';

    public ?string $meta_phone_number_id = '';

    public ?string $meta_waba_id = '';

    public ?string $meta_access_token = '';

    public ?string $bank_name = '';

    public ?string $account_number = '';

    public ?string $account_owner_name = '';

    public int $currentStep = 1;

    public bool $isTestingConnection = false;

    public ?string $connectionStatus = null; // 'success' | 'error' | null

    public ?string $connectionMessage = null;

    public bool $showAccessToken = false;

    public bool $isOnboarding = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $this->isOnboarding = ! request()->routeIs('store.edit') && ! request()->is('settings/*');

        $profile = $user->storeProfile;

        if ($profile) {
            $this->store_name = $profile->store_name ?? '';
            $this->meta_phone_number_id = $profile->meta_phone_number_id ?? '';
            $this->meta_waba_id = $profile->meta_waba_id ?? '';
            $this->meta_access_token = $profile->meta_access_token ?? '';
            $this->bank_name = $profile->bank_name ?? '';
            $this->account_number = $profile->account_number ?? '';
            $this->account_owner_name = $profile->account_owner_name ?? '';
        } else {
            $this->store_name = $user->name ? $user->name."'s Store" : '';
        }
    }

    /**
     * Navigate to a specific step.
     */
    public function setStep(int $step): void
    {
        if ($step > $this->currentStep) {
            $this->validateCurrentStep();
        }

        $this->currentStep = max(1, min(3, $step));
    }

    /**
     * Move to the next step.
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep = min(3, $this->currentStep + 1);
    }

    /**
     * Move to the previous step.
     */
    public function previousStep(): void
    {
        $this->currentStep = max(1, $this->currentStep - 1);
    }

    /**
     * Validate fields for the current step.
     */
    protected function validateCurrentStep(): void
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'store_name' => ['required', 'string', 'min:2', 'max:255'],
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'meta_phone_number_id' => ['nullable', 'string', 'max:255'],
                'meta_waba_id' => ['nullable', 'string', 'max:255'],
                'meta_access_token' => ['nullable', 'string'],
            ]);
        }
    }

    /**
     * Toggle visibility of Meta Access Token.
     */
    public function toggleShowAccessToken(): void
    {
        $this->showAccessToken = ! $this->showAccessToken;
    }

    /**
     * Test connection to Meta WhatsApp Cloud API.
     */
    public function testMetaConnection(): void
    {
        $this->validate([
            'meta_phone_number_id' => ['required', 'string', 'max:255'],
            'meta_access_token' => ['required', 'string'],
        ], [
            'meta_phone_number_id.required' => 'Phone Number ID wajib diisi untuk menguji koneksi.',
            'meta_access_token.required' => 'Meta Access Token wajib diisi untuk menguji koneksi.',
        ]);

        $this->isTestingConnection = true;
        $this->connectionStatus = null;
        $this->connectionMessage = null;

        try {
            $phoneNumberId = trim((string) $this->meta_phone_number_id);
            $token = trim((string) $this->meta_access_token);

            $response = Http::timeout(10)
                ->withToken($token)
                ->get("https://graph.facebook.com/v21.0/{$phoneNumberId}");

            if ($response->successful()) {
                $data = $response->json();
                $verifiedName = $data['verified_name'] ?? $data['display_phone_number'] ?? 'WhatsApp Cloud API';
                $this->connectionStatus = 'success';
                $this->connectionMessage = "Koneksi berhasil! Terhubung ke: {$verifiedName}";
                Flux::toast(variant: 'success', text: __('Meta WhatsApp API connection successful!'));
            } else {
                $errorData = $response->json('error');
                $errMsg = is_array($errorData) ? ($errorData['message'] ?? 'Kredensial tidak valid.') : 'Respon gagal dari server Meta.';
                $this->connectionStatus = 'error';
                $this->connectionMessage = "Gagal terhubung: {$errMsg}";
                Flux::toast(variant: 'danger', text: __('Meta API connection failed.'));
            }
        } catch (\Throwable $e) {
            $this->connectionStatus = 'error';
            $this->connectionMessage = 'Gagal menghubungi Meta API: '.$e->getMessage();
            Flux::toast(variant: 'danger', text: __('Network error while testing Meta API.'));
        } finally {
            $this->isTestingConnection = false;
        }
    }

    /**
     * Save the store profile and Meta API keys.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'store_name' => ['required', 'string', 'min:2', 'max:255'],
            'meta_phone_number_id' => ['nullable', 'string', 'max:255'],
            'meta_waba_id' => ['nullable', 'string', 'max:255'],
            'meta_access_token' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'account_owner_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        $user->storeProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => trim($validated['store_name']),
                'meta_phone_number_id' => $validated['meta_phone_number_id'] ? trim($validated['meta_phone_number_id']) : null,
                'meta_waba_id' => $validated['meta_waba_id'] ? trim($validated['meta_waba_id']) : null,
                'meta_access_token' => $validated['meta_access_token'] ? trim($validated['meta_access_token']) : null,
                'bank_name' => $validated['bank_name'] ? trim($validated['bank_name']) : null,
                'account_number' => $validated['account_number'] ? trim($validated['account_number']) : null,
                'account_owner_name' => $validated['account_owner_name'] ? trim($validated['account_owner_name']) : null,
            ]
        );

        Flux::toast(variant: 'success', text: __('Store profile and Meta API keys saved successfully.'));

        if ($this->isOnboarding) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }

    /**
     * Skip onboarding flow for now and proceed to dashboard.
     */
    public function skip(): void
    {
        $user = Auth::user();

        if ($user && ! $user->storeProfile) {
            $user->storeProfile()->create([
                'store_name' => $this->store_name ?: ($user->name."'s Store"),
            ]);
        }

        Flux::toast(text: __('You can configure your Meta API credentials anytime in Settings.'));

        $this->redirectRoute('dashboard', navigate: true);
    }

    /**
     * Render the Livewire component.
     */
    public function render(): View
    {
        $view = view('livewire.auth.store-setup');

        if (request()->routeIs('store.edit') || request()->is('settings/*')) {
            return $view->layout('layouts.app');
        }

        return $view->layout('layouts.auth.card');
    }
}
