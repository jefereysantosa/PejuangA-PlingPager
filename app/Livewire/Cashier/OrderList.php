<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderList extends Component
{
    public string $statusFilter = 'active';

    #[On('order-updated')]
    public function refreshList(): void
    {
        // Trigger re-render setelah OrderEditor menyimpan perubahan
    }

    public function markAsReady(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => 'ready']);
    }

    public function editOrder(int $orderId): void
    {
        $this->dispatch('open-order-editor', orderId: $orderId);
    }

    public function render(): View
    {
        $orders = Order::with('orderItems.product')
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('status', 'processing'))
            ->when($this->statusFilter === 'ready', fn ($q) => $q->where('status', 'ready'))
            ->latest()
            ->get();

        return view('livewire.cashier.order-list', [
            'orders' => $orders,
        ]);
    }
}
