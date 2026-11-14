<?php

namespace App\Livewire\Consumer;

use App\Models\Order;
use App\Models\StoreProfile;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.mobile')]
class MobilePager extends Component
{
    public const PLATFORM_FEE = 500;

    public string $orderCode;

    public ?Order $order = null;

    public function mount(string $orderCode): void
    {
        $this->orderCode = $orderCode;
        $this->loadOrder();
    }

    public function refreshOrder(): void
    {
        $this->loadOrder();
    }

    protected function loadOrder(): void
    {
        $this->order = Order::with(['orderItems.product', 'user'])
            ->where('order_code', $this->orderCode)
            ->firstOrFail();
    }

    #[Computed]
    public function storeProfile(): ?StoreProfile
    {
        return $this->findStoreProfile();
    }

    #[Computed]
    public function subtotal(): int
    {
        return $this->calculateSubtotal();
    }

    #[Computed]
    public function grandTotal(): int
    {
        return $this->calculateSubtotal() + self::PLATFORM_FEE;
    }

    /** @return array{label: string, class: string, step: int} */
    #[Computed]
    public function statusMeta(): array
    {
        return $this->buildStatusMeta();
    }

    #[Computed]
    public function whatsappUrl(): string
    {
        $number = preg_replace('/\D+/', '', (string) $this->order->customer_wa_number);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        $message = "Hi! I'd like to receive order status updates.\n"
            ."Order: {$this->order->order_code}\n"
            .'Store: '.($this->findStoreProfile()->store_name ?? 'PlingPager Store')."\n"
            ."Status: {$this->buildStatusMeta()['label']}\n"
            .'Track live: '.route('consumer.pager', $this->order->order_code);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }

    /** @return array{label: string, class: string, step: int} */
    protected function buildStatusMeta(): array
    {
        return match ($this->order->status) {
            'processing' => ['label' => 'On Process', 'class' => 'bg-pling-accent text-white', 'step' => 2],
            'ready' => ['label' => 'Ready to Pickup', 'class' => 'bg-pling-green text-pling-primary', 'step' => 3],
            'completed' => ['label' => 'Completed', 'class' => 'bg-pling-primary text-white', 'step' => 3],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-zinc-300 text-zinc-700', 'step' => 0],
        };
    }

    protected function calculateSubtotal(): int
    {
        return (int) $this->order->orderItems->sum(
            fn ($item) => $item->price_at_time * $item->quantity
        );
    }

    protected function findStoreProfile(): ?StoreProfile
    {
        return StoreProfile::where('user_id', $this->order->user_id)->first();
    }

    public function render(): View
    {
        return view('livewire.consumer.mobile-pager');
    }
}
