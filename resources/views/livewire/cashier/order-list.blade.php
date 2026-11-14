<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <flux:heading size="xl">Antrean Pesanan</flux:heading>
            <flux:subheading>Pantau pesanan aktif, ubah catatan, atau tandai pesanan siap diambil.</flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button size="sm" :variant="$statusFilter === 'active' ? 'primary' : 'ghost'" wire:click="$set('statusFilter', 'active')">
                Antrean Aktif
            </flux:button>
            <flux:button size="sm" :variant="$statusFilter === 'ready' ? 'primary' : 'ghost'" wire:click="$set('statusFilter', 'ready')">
                Ready
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($orders as $order)
            <div class="flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <flux:heading size="lg">{{ $order->customer_name }}</flux:heading>
                                <flux:badge size="sm" :color="$order->status === 'ready' ? 'green' : 'amber'">
                                    {{ strtoupper($order->status) }}
                                </flux:badge>
                            </div>
                            <flux:text class="text-xs">{{ $order->order_code }} • {{ $order->created_at->diffForHumans() }}</flux:text>
                        </div>

                        {{-- Tombol Gear untuk membuka OrderEditor --}}
                        <flux:button size="sm" variant="ghost" icon="cog-6-tooth" wire:click="editOrder({{ $order->id }})" title="Edit Catatan / Status" />
                    </div>

                    {{-- Daftar Item Pesanan --}}
                    <ul class="mt-4 space-y-1.5 border-t border-zinc-100 pt-3 text-sm dark:border-zinc-800">
                        @foreach ($order->orderItems as $item)
                            <li class="flex justify-between">
                                <span class="text-zinc-700 dark:text-zinc-300">
                                    {{ $item->quantity }}x {{ $item->product->name ?? 'Produk' }}
                                </span>
                                <span class="text-zinc-500">
                                    Rp {{ number_format($item->price_at_time * $item->quantity, 0, ',', '.') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if (!empty($order->notes))
                        <div class="mt-3 rounded-lg bg-amber-50 p-2.5 text-xs text-amber-800 dark:bg-amber-950/50 dark:text-amber-200">
                            <strong>Catatan:</strong> {{ $order->notes }}
                        </div>
                    @endif
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                    <span class="font-semibold text-zinc-900 dark:text-white">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>

                    @if ($order->status !== 'ready')
                        <flux:button size="sm" variant="primary" icon="check" wire:click="markAsReady({{ $order->id }})">
                            Tandai Ready
                        </flux:button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
                <flux:text>Tidak ada pesanan pada kategori ini.</flux:text>
            </div>
        @endforelse
    </div>

    {{-- Modal OrderEditor --}}
    <livewire:cashier.order-editor />
</div>