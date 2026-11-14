<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderFinalize extends Component
{
    public bool $showModal = false;

    public ?Order $order = null;

    #[On('order-created')]
    public function openOrder(int $orderId): void
    {
        $this->order = Order::with('items.product')->find($orderId);
        $this->showModal = (bool) $this->order;
    }

    public function getTrackingUrlProperty(): string
    {
        if (! $this->order) {
            return '';
        }

        // Sesuaikan route tracking jika sudah dibuat oleh member lain
        return url('/track/'.($this->order->order_number ?? $this->order->id));
    }

    public function getQrCodeUrlProperty(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($this->trackingUrl);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->order = null;
    }

    public function render()
    {
        return view('livewire.cashier.order-finalize');
    }
}
