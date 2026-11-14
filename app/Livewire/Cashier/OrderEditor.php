<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderEditor extends Component
{
    public bool $showModal = false;

    public ?int $orderId = null;

    public string $consumerName = '';

    public string $notes = '';

    public string $status = 'processing';

    #[On('open-order-editor')]
    public function loadOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        $this->orderId = $order->id;
        $this->consumerName = $order->customer_name;
        $this->notes = $order->notes ?? '';
        $this->status = $order->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'consumerName' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:processing,ready,completed,cancelled'],
        ]);

        $order = Order::findOrFail($this->orderId);
        $order->update([
            'customer_name' => $this->consumerName,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);

        $this->showModal = false;
        $this->dispatch('order-updated');
    }

    public function render(): View
    {
        return view('livewire.cashier.order-editor');
    }
}
