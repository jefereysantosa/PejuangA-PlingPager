<?php

use App\Livewire\Auth\StoreSetup;
use Illuminate\Support\Facades\Route;
use App\Livewire\Cashier\OrderList;
use App\Livewire\Cashier\OrderTaker;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cashier/pos', OrderTaker::class)->name('cashier.pos');
    Route::get('/cashier/orders', OrderList::class)->name('cashier.orders');
});

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('store-setup', StoreSetup::class)->name('store.setup');
    Route::livewire('onboarding', StoreSetup::class)->name('onboarding');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
