<?php

namespace Tests\Feature\Cashier;

use App\Livewire\Cashier\ProfileEditor;
use App\Models\StoreProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_editor_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->assertOk()
            ->assertSee('Edit Profil & Rekening');
    }

    public function test_profile_editor_loads_existing_store_and_bank_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Pak Budi',
        ]);

        StoreProfile::create([
            'user_id' => $user->id,
            'store_name' => 'Kedai Kopi Mantap',
            'bank_name' => 'BCA',
            'account_number' => '5544332211',
            'account_owner_name' => 'Budi Santoso',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->assertSet('name', 'Pak Budi')
            ->assertSet('store_name', 'Kedai Kopi Mantap')
            ->assertSet('bank_name', 'BCA')
            ->assertSet('account_number', '5544332211')
            ->assertSet('account_owner_name', 'Budi Santoso');
    }

    public function test_profile_editor_modal_can_be_opened_and_closed(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->assertSet('showModal', false)
            ->call('open')
            ->assertSet('showModal', true)
            ->call('close')
            ->assertSet('showModal', false);
    }

    public function test_profile_editor_opens_via_event(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->assertSet('showModal', false)
            ->dispatch('open-profile-editor')
            ->assertSet('showModal', true);
    }

    public function test_quick_bank_selection(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->call('selectBank', 'Mandiri')
            ->assertSet('bank_name', 'Mandiri');
    }

    public function test_cashier_can_update_bank_accounts_and_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
        ]);

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->call('open')
            ->set('name', 'Budi Kasir')
            ->set('store_name', 'Kedai Baru 88')
            ->set('bank_name', 'BCA')
            ->set('account_number', '1234567890')
            ->set('account_owner_name', 'Budi Kasir')
            ->call('save')
            ->assertSet('showModal', false)
            ->assertDispatched('profile-updated');

        // Assert user name updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Budi Kasir',
        ]);

        // Assert store profile & bank account updated
        $this->assertDatabaseHas('store_profiles', [
            'user_id' => $user->id,
            'store_name' => 'Kedai Baru 88',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_owner_name' => 'Budi Kasir',
        ]);
    }

    public function test_required_validation_for_profile_editor(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ProfileEditor::class)
            ->set('name', '')
            ->set('store_name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'store_name' => 'required']);
    }
}
