<?php

namespace App\Livewire\Cashier;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderFinalize extends Component
{
    public bool $showModal = false;

    public ?Order $order = null;

    #[On('order-created')]
    public function openOrder(int $orderId): void
    {
        $this->order = Order::with('orderItems.product')->find($orderId);
        $this->showModal = (bool) $this->order;
    }

    public function getTrackingUrlProperty(): string
    {
        if (! $this->order) {
            return '';
        }

        // Sesuaikan route tracking jika sudah dibuat oleh member lain
        return route('consumer.track', ['orderCode' => $this->order->order_code]);
    }

    public function getQrCodeUrlProperty(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($this->getTrackingUrlProperty());
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->order = null;
    }

    public function render(): View
    {
        return view('livewire.cashier.order-finalize');
    }
}
