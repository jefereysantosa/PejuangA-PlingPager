<div class="flex flex-col gap-6 lg:flex-row">
    {{-- Kiri: Daftar Produk --}}
    <div class="flex-1 space-y-4">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <flux:heading size="xl">POS Kasir</flux:heading>
                <flux:subheading>Pilih produk untuk ditambahkan ke keranjang pesanan.</flux:subheading>
            </div>
            <div class="w-full sm:w-64">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari produk..." />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($products as $product)
                <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-4 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <flux:heading size="lg">{{ $product->name }}</flux:heading>
                        @if(!empty($product->description))
                            <flux:text class="mt-1 line-clamp-2 text-sm">{{ $product->description }}</flux:text>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <span class="font-semibold text-zinc-900 dark:text-white">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        <flux:button size="sm" variant="primary" icon="plus" wire:click="addToCart({{ $product->id }})">
                            Tambah
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                    <flux:text>Produk tidak ditemukan.</flux:text>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Kanan: Keranjang & Checkout --}}
    <div class="w-full lg:w-96">
        <div class="sticky top-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-700">
                <flux:heading size="lg">Keranjang Pesanan</flux:heading>
                @if(count($cart) > 0)
                    <flux:button size="xs" variant="ghost" wire:click="clearCart">Kosongkan</flux:button>
                @endif
            </div>

            <div class="mt-4 space-y-3 max-h-80 overflow-y-auto">
                @forelse ($cart as $item)
                    <div class="flex items-center justify-between gap-2 rounded-lg border border-zinc-100 p-2.5 dark:border-zinc-800">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $item['name'] }}</p>
                            <p class="text-xs text-zinc-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <flux:button size="xs" icon="minus" wire:click="updateQty({{ $item['id'] }}, -1)" />
                            <span class="w-6 text-center text-sm font-semibold">{{ $item['qty'] }}</span>
                            <flux:button size="xs" icon="plus" wire:click="updateQty({{ $item['id'] }}, 1)" />
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <flux:text>Belum ada item di keranjang.</flux:text>
                    </div>
                @endforelse
            </div>

            <form wire:submit="submitOrder" class="mt-5 space-y-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <flux:input wire:model="consumerName" label="Nama Konsumen" placeholder="Contoh: Budi" required />
                
                <flux:textarea wire:model="notes" label="Catatan Pesanan (Opsional)" rows="2" placeholder="Contoh: Es sedikit, pedas..." />

                <div class="flex items-center justify-between text-base font-bold text-zinc-900 dark:text-white">
                    <span>Total Bayar</span>
                    <span>Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>

                <flux:button type="submit" variant="primary" class="w-full" :disabled="count($cart) === 0">
                    Konfirmasi & Buat Pesanan
                </flux:button>
            </form>
        </div>
    </div>

    {{-- Komponen Modal Finalisasi Order & QR Code --}}
    <livewire:cashier.order-finalize />
</div>