<?php

namespace App\Livewire\Cashier;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MenuForm extends Component
{
    public ?int $productId = null;

    public string $name = '';

    public string $category = 'meal';

    public string $price = '';

    public bool $isAvailable = true;

    public string $description = '';

    public function mount(?int $productId = null): void
    {
        $this->productId = $productId;

        if ($productId) {
            $product = Product::where('user_id', Auth::id())->findOrFail($productId);

            $this->name = $product->name;
            $this->category = $product->category;
            $this->price = (string) $product->price;
            $this->isAvailable = (bool) $product->is_available;
            $this->description = $product->description ?? '';
        }
    }

    #[Computed]
    public function isEdit(): bool
    {
        return $this->productId !== null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'in:meal,beverage'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'isAvailable' => ['boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama menu wajib diisi.',
            'name.max' => 'Nama menu maksimal 120 karakter.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat (tanpa titik/koma).',
            'price.min' => 'Harga tidak boleh negatif.',
            'price.max' => 'Harga terlalu besar.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
        ]);

        $payload = [
            'name' => trim($validated['name']),
            'category' => $validated['category'],
            'price' => (int) $validated['price'],
            'is_available' => $this->isAvailable,
            'description' => trim($this->description) ?: null,
        ];

        if ($this->productId) {
            Product::where('user_id', Auth::id())->findOrFail($this->productId)->update($payload);
            $message = 'Perubahan "'.$payload['name'].'" disimpan.';
        } else {
            Product::create($payload + ['user_id' => Auth::id()]);
            $message = '"'.$payload['name'].'" ditambahkan ke menu.';
        }

        $this->dispatch('product-saved', isEdit: $this->isEdit);
        Flux::toast(text: $message, variant: 'success');

        if (! $this->isEdit) {
            $this->reset(['name', 'price', 'description']);
            $this->category = 'meal';
            $this->isAvailable = true;
        }
    }

    public function delete(): void
    {
        if (! $this->productId) {
            return;
        }

        $product = Product::where('user_id', Auth::id())->findOrFail($this->productId);
        $name = $product->name;
        $id = $product->id;
        $product->delete();

        $this->dispatch('product-deleted', productId: $id, productName: $name);
    }

    public function close(): void
    {
        $this->dispatch('product-saved', isEdit: true);
    }

    public function render()
    {
        return view('livewire.cashier.menu-form');
    }
}
