<?php

use App\Livewire\Auth\StoreSetup;
use App\Livewire\Cashier\MenuManager;
use App\Livewire\Cashier\OrderList;
use App\Livewire\Cashier\OrderTaker;
use App\Livewire\Consumer\MobilePager;
use App\Models\Order;
use App\Models\StoreProfile;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/cashier/pos', OrderTaker::class)->name('cashier.pos');
    Route::get('/cashier/orders', OrderList::class)->name('cashier.orders');
    Route::get('/cashier/menu', MenuManager::class)->name('cashier.menu');
});

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('store-setup', StoreSetup::class)->name('store.setup');
    Route::livewire('onboarding', StoreSetup::class)->name('onboarding');
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// ── Consumer Pager (publik, diakses via QR per pesanan) ──────────────────────
Route::get('/pager/{orderCode}', MobilePager::class)->name('consumer.pager');
Route::get('/track/{orderCode}', MobilePager::class)->name('consumer.track'); // alias utk QR OrderFinalize

Route::get('/pager/{orderCode}/receipt', function (string $orderCode) {
    $order = Order::with(['orderItems.product', 'user'])->where('order_code', $orderCode)->firstOrFail();
    $subtotal = (int) $order->orderItems->sum(fn ($item) => $item->price_at_time * $item->quantity);
    $platformFee = MobilePager::PLATFORM_FEE;

    return view('consumer.receipt-print', [
        'order' => $order,
        'storeProfile' => StoreProfile::where('user_id', $order->user_id)->first(),
        'subtotal' => $subtotal,
        'platformFee' => $platformFee,
        'grandTotal' => $subtotal + $platformFee,
    ]);
})->name('consumer.pager.receipt');

Route::get('/pager/{orderCode}/receipt.txt', function (string $orderCode) {
    $order = Order::with(['orderItems.product', 'user'])->where('order_code', $orderCode)->firstOrFail();
    $subtotal = (int) $order->orderItems->sum(fn ($item) => $item->price_at_time * $item->quantity);
    $platformFee = MobilePager::PLATFORM_FEE;
    $storeName = StoreProfile::where('user_id', $order->user_id)->value('store_name') ?? 'PlingPager Store';

    $lines = [
        'PLINGPAGER — E-RECEIPT',
        $storeName,
        str_repeat('-', 32),
        'Order ID : '.$order->order_code,
        'Recipient: '.$order->customer_name,
        'Date     : '.$order->created_at->format('d M Y H:i'),
        'Status   : '.ucfirst($order->status),
        str_repeat('-', 32),
    ];

    foreach ($order->orderItems as $i => $item) {
        $lines[] = ($i + 1).'. '.($item->product->name ?? 'Menu dihapus');
        $lines[] = '   '.$item->quantity.' x Rp '.number_format($item->price_at_time, 0, ',', '.').' = Rp '.number_format($item->price_at_time * $item->quantity, 0, ',', '.');
    }

    if ($order->notes) {
        $lines[] = 'Note: '.$order->notes;
    }

    $lines[] = str_repeat('-', 32);
    $lines[] = 'Subtotal    : Rp '.number_format($subtotal, 0, ',', '.');
    $lines[] = 'Platform Fee: Rp '.number_format($platformFee, 0, ',', '.');
    $lines[] = 'TOTAL       : Rp '.number_format($subtotal + $platformFee, 0, ',', '.');
    $lines[] = str_repeat('-', 32);
    $lines[] = 'Track: '.route('consumer.pager', $order->order_code);

    return response(implode("\n", $lines), 200, [
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="e-receipt-'.$order->order_code.'.txt"',
    ]);
})->name('consumer.pager.receipt.txt');

require __DIR__.'/settings.php';
