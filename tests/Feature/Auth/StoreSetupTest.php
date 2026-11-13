<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\StoreSetup;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class StoreSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_store_setup(): void
    {
        $response = $this->get(route('store.setup'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_merchant_can_render_store_setup(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('store.setup'));

        $response->assertOk();
    }

    public function test_merchant_can_input_store_name_and_meta_api_keys(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StoreSetup::class)
            ->set('store_name', 'Kedai Kopi Nikmat')
            ->set('meta_phone_number_id', '123456789012345')
            ->set('meta_waba_id', '987654321098765')
            ->set('meta_access_token', 'EAAGSampleToken123')
            ->set('bank_name', 'BCA')
            ->set('account_number', '0987654321')
            ->set('account_owner_name', 'Budi Santoso')
            ->call('save')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('store_profiles', [
            'user_id' => $user->id,
            'store_name' => 'Kedai Kopi Nikmat',
            'meta_phone_number_id' => '123456789012345',
            'meta_waba_id' => '987654321098765',
            'meta_access_token' => 'EAAGSampleToken123',
            'bank_name' => 'BCA',
            'account_number' => '0987654321',
            'account_owner_name' => 'Budi Santoso',
        ]);
    }

    public function test_store_name_is_required_to_save(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StoreSetup::class)
            ->set('store_name', '')
            ->call('save')
            ->assertHasErrors(['store_name' => 'required']);
    }

    public function test_merchant_can_navigate_steps(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StoreSetup::class)
            ->set('store_name', 'Kedai Mantap')
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->call('previousStep')
            ->assertSet('currentStep', 2);
    }

    public function test_merchant_can_skip_onboarding(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StoreSetup::class)
            ->call('skip')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('store_profiles', [
            'user_id' => $user->id,
        ]);
    }

    public function test_meta_connection_testing(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response([
                'verified_name' => 'Kedai Pejuang Official',
                'display_phone_number' => '+62 812 3456 7890',
            ], 200),
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StoreSetup::class)
            ->set('store_name', 'Kedai Pejuang')
            ->set('currentStep', 2)
            ->set('meta_phone_number_id', '1234567890')
            ->set('meta_access_token', 'EAAGvalidtoken')
            ->call('testMetaConnection')
            ->assertSet('connectionStatus', 'success')
            ->assertSee('Kedai Pejuang Official');
    }
}
