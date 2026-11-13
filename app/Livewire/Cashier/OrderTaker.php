<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class OrderTaker extends Component
{
    public string $search = '';
    public string $consumerName = '';
    public string $notes = '';
    
    /** @var array<int, array{id: int, name: string, price: float|int, qty: int}> */
    public array $cart = [];

    public ?int $finalizedOrderId = null;

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
            ];
        }
    }

    public function updateQty(int $productId, int $delta): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $newQty = $this->cart[$productId]['qty'] + $delta;

        if ($newQty <= 0) {
            unset($this->cart[$productId]);
        } else {
            $this->cart[$productId]['qty'] = $newQty;
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->consumerName = '';
        $this->notes = '';
    }

    public function getTotalProperty(): float|int
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function submitOrder(): void
    {
        $this->validate([
            'consumerName' => ['required', 'string', 'max:255'],
            'cart' => ['required', 'array', 'min:1'],
        ], [
            'consumerName.required' => 'Nama konsumen wajib diisi.',
            'cart.min' => 'Keranjang pesanan masih kosong.',
        ]);

        $order = DB::transaction(function () {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                'consumer_name' => $this->consumerName,
                'total_price' => $this->total,
                'status' => 'pending', // atau 'preparing' sesuai enum di migrasi
                'notes' => $this->notes,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
            }

            return $order;
        });

        $this->clearCart();
        $this->finalizedOrderId = $order->id;
        $this->dispatch('order-created', orderId: $order->id);
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->get();

        return view('livewire.cashier.order-taker', [
            'products' => $products,
        ]);
    }
}