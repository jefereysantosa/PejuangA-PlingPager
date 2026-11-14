<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.mobile')]
class MenuManager extends Component
{
    public string $search = '';

    public string $categoryFilter = 'all';

    public string $availabilityFilter = 'all';

    public string $sortBy = 'latest';

    public string $activeTab = 'settings';

    public bool $showEditSheet = false;

    public ?int $editingProductId = null;

    #[On('product-saved')]
    public function handleProductSaved(bool $isEdit = false): void
    {
        if ($isEdit) {
            $this->closeEditSheet();
        } else {
            $this->activeTab = 'settings';
        }
    }

    #[On('product-deleted')]
    public function handleProductDeleted(int $productId, string $productName = ''): void
    {
        $this->closeEditSheet();

        $product = Product::where('user_id', Auth::id())->find($productId);

        if (! $product) {
            return;
        }

        Cache::put('menu-manager:deleted-product:'.$productId, $product->toArray(), now()->addSeconds(30));
        $product->delete();

        flux()->toast(
            text: '"'.($productName ?: $product->name).'" dihapus.',
            variant: 'warning',
            duration: 8000,
        )->action(text: 'Urungkan', method: 'undoDelete', params: [$productId]);
    }

    public function undoDelete(int $productId): void
    {
        $data = Cache::pull('menu-manager:deleted-product:'.$productId);

        if (! $data) {
            flux()->toast(text: 'Batas waktu urung sudah habis.', variant: 'danger');

            return;
        }

        $product = new Product;
        $product->setRawAttributes($data, true);
        $product->save();

        flux()->toast(text: '"'.$product->name.'" dikembalikan.', variant: 'success');
    }

    public function toggleStore(): void
    {
        $profile = StoreProfile::firstOrCreate(
            ['user_id' => Auth::id()],
            ['store_name' => Auth::user()->name."'s Store"],
        );

        $profile->update(['is_open' => ! $profile->is_open]);

        flux()->toast(
            text: $profile->is_open ? 'Toko dibuka. Selamat berjualan!' : 'Toko ditutup.',
            variant: $profile->is_open ? 'success' : 'warning',
        );
    }

    public function toggleAvailability(int $productId): void
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($productId);
        $product->update(['is_available' => ! $product->is_available]);

        flux()->toast(
            text: $product->name.' sekarang '.($product->is_available ? 'tersedia' : 'tidak tersedia').'.',
            variant: $product->is_available ? 'success' : 'warning',
            duration: 2500,
        );
    }

    public function openEditSheet(int $productId): void
    {
        $this->editingProductId = $productId;
        $this->showEditSheet = true;
    }

    public function closeEditSheet(): void
    {
        $this->showEditSheet = false;
        $this->editingProductId = null;
    }

    public function render()
    {
        $profile = StoreProfile::where('user_id', Auth::id())->first();

        $products = Product::query()
            ->where('user_id', Auth::id())
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->categoryFilter !== 'all', fn ($q) => $q->where('category', $this->categoryFilter))
            ->when($this->availabilityFilter === 'available', fn ($q) => $q->where('is_available', true))
            ->when($this->availabilityFilter === 'unavailable', fn ($q) => $q->where('is_available', false))
            ->when($this->sortBy === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sortBy === 'price_low', fn ($q) => $q->orderBy('price'))
            ->when($this->sortBy === 'price_high', fn ($q) => $q->orderByDesc('price'))
            ->when($this->sortBy === 'latest', fn ($q) => $q->latest())
            ->get()
            ->groupBy('category');

        $latestOrderCode = Order::where('user_id', Auth::id())->latest()->value('order_code');

        return view('livewire.cashier.menu-manager', [
            'profile' => $profile,
            'meals' => $products->get('meal', collect()),
            'beverages' => $products->get('beverage', collect()),
            'latestOrderCode' => $latestOrderCode,
        ]);
    }
}
