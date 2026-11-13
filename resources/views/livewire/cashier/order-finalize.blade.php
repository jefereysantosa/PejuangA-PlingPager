<div>
    @if ($showModal && $order)
        <flux:modal wire:model="showModal" class="max-w-md">
            <div class="space-y-5 text-center">
                <div>
                    <flux:badge color="green" size="sm">Pesanan Berhasil Dibuat</flux:badge>
                    <flux:heading size="xl" class="mt-2">{{ $order->order_number ?? '#' . $order->id }}</flux:heading>
                    <flux:subheading>Atas nama: <strong>{{ $order->consumer_name }}</strong></flux:subheading>
                </div>

                {{-- QR Code Tracking --}}
                <div class="flex flex-col items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                    <img src="{{ $this->qrCodeUrl }}" alt="QR Tracking Pesanan" class="h-44 w-44 rounded-lg bg-white p-2 shadow-xs" />
                    <flux:text class="mt-3 text-xs">Scan QR code di atas untuk memantau status pesanan.</flux:text>
                </div>

                {{-- Link Tracking --}}
                <div class="text-left">
                    <flux:input label="Link Tracking Konsumen" :value="$this->trackingUrl" readonly copyable />
                </div>

                {{-- Ringkasan Total --}}
                <div class="flex items-center justify-between rounded-lg bg-zinc-100 px-4 py-3 text-sm font-semibold dark:bg-zinc-800">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-end gap-2">
                    <flux:button variant="primary" class="w-full" wire:click="closeModal">
                        Selesai & Transaksi Baru
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>