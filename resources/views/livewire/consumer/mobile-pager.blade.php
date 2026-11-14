<div wire:poll.5s="refreshOrder">
    <div class="mx-auto w-full max-w-sm rounded-[20px] bg-pling-neutral p-5 shadow-2xl">
        {{-- Logo --}}
        <div class="flex justify-center">
            <x-pling-logo :size="26" text-class="text-lg" />
        </div>

        {{-- Header toko --}}
        <div class="mt-4 flex items-center gap-3">
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-zinc-200 text-lg font-extrabold text-pling-secondary">
                {{ strtoupper(substr($this->storeProfile?->store_name ?? 'P', 0, 1)) }}
            </div>
            <h1 class="min-w-0 flex-1 truncate text-xl font-extrabold text-pling-primary">
                {{ $this->storeProfile?->store_name ?? 'PlingPager Store' }}
            </h1>
        </div>

        {{-- Status pesanan --}}
        <div class="mt-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-extrabold text-pling-primary">Your order status :</p>
                <span class="flex items-center gap-1 text-[10px] font-bold text-pling-secondary">
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-pling-green opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-pling-green"></span>
                    </span>
                    LIVE
                </span>
            </div>
            <div class="mt-2 flex justify-center">
                <span wire:key="status-{{ $order->status }}"
                    class="animate-pop rounded-2xl px-8 py-2.5 text-2xl font-extrabold shadow-md {{ $this->statusMeta['class'] }}">
                    {{ $this->statusMeta['label'] }}
                </span>
            </div>

            {{-- Tracker langkah --}}
            @if($order->status !== 'cancelled')
                <div class="mt-4 flex items-start">
                    @foreach(['Received', 'On Process', 'Ready'] as $i => $stepLabel)
                        @php
                            $stepNumber = $i + 1;
                            $currentStep = $this->statusMeta['step'];
                            $isDone = $stepNumber < $currentStep || $order->status === 'completed';
                            $isCurrent = $stepNumber === $currentStep && $order->status !== 'completed';
                        @endphp
                        @if($i > 0)
                            <div class="mx-1 mt-[13px] h-0.5 flex-1 rounded-full {{ $stepNumber <= $currentStep || $order->status === 'completed' ? 'bg-pling-primary' : 'bg-zinc-300' }}"></div>
                        @endif
                        <div class="flex w-16 flex-col items-center gap-1">
                            <span class="flex size-7 items-center justify-center rounded-full text-[11px] font-extrabold transition
                                {{ $isDone ? 'bg-pling-primary text-white' : ($isCurrent ? 'bg-pling-accent text-white ring-4 ring-pling-accent/25' : 'bg-zinc-200 text-zinc-400') }}">
                                @if($isDone)
                                    <svg class="size-4" fill="currentColor"><use href="#i-check"/></svg>
                                @else
                                    {{ $stepNumber }}
                                @endif
                            </span>
                            <span class="text-center text-[9px] font-bold leading-tight {{ $isDone || $isCurrent ? 'text-pling-primary' : 'text-zinc-400' }}">{{ $stepLabel }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 rounded-xl bg-red-50 px-3 py-2 text-center text-xs font-semibold text-red-500">
                    Pesanan ini dibatalkan. Hubungi toko untuk info lebih lanjut.
                </p>
            @endif
        </div>

        {{-- E-Receipt --}}
        <div class="mt-5">
            <p class="text-sm font-extrabold text-pling-primary">Your E-Receipt</p>
            <div class="mt-2 rounded-xl border border-pling-secondary/60 bg-white/60 p-3">
                <p class="text-xs font-extrabold text-pling-primary">{{ $order->customer_name }}</p>
                <p class="text-[10px] font-semibold text-pling-secondary">Order ID : {{ $order->order_code }}</p>

                <div class="mt-2 border-t border-pling-secondary/30 pt-2">
                    <div class="flex items-center justify-between text-[10px] font-extrabold text-pling-primary">
                        <span>Orders</span>
                        <span>Price</span>
                    </div>
                    <ol class="mt-1 space-y-1.5">
                        @foreach($order->orderItems as $index => $item)
                            <li class="text-[11px] leading-snug text-pling-primary">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-bold">{{ $index + 1 }}. {{ $item->product?->name ?? 'Menu dihapus' }}</span>
                                    <span class="shrink-0 font-semibold text-pling-secondary">{{ number_format($item->price_at_time * $item->quantity, 0, ',', '.') }}</span>
                                </div>
                                <p class="pl-3 text-[10px] text-pling-secondary">Qty : {{ $item->quantity }} × Rp {{ number_format($item->price_at_time, 0, ',', '.') }}</p>
                            </li>
                        @endforeach
                    </ol>
                    @if($order->notes)
                        <p class="mt-1.5 pl-3 text-[10px] italic text-pling-secondary">Note : {{ $order->notes }}</p>
                    @endif
                </div>

                <div class="mt-2 space-y-1 border-t border-pling-secondary/30 pt-2 text-[11px]">
                    <div class="flex items-center justify-between text-pling-secondary">
                        <span>Subtotal</span>
                        <span>{{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-pling-secondary">
                        <span>Platform Fee</span>
                        <span>{{ number_format(\App\Livewire\Consumer\MobilePager::PLATFORM_FEE, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-pling-secondary/30 pt-1 text-xs font-extrabold text-pling-primary">
                        <span>Total</span>
                        <span>Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('consumer.pager.receipt', $order->order_code) }}" target="_blank"
                        class="flex items-center justify-center gap-1 rounded-full bg-pling-secondary py-1.5 text-[10px] font-extrabold text-white transition hover:bg-pling-primary active:scale-95">
                        <svg class="size-3.5" fill="currentColor"><use href="#i-receipt"/></svg>
                        Download e-receipt
                    </a>
                    <a href="{{ route('consumer.pager.receipt.txt', $order->order_code) }}"
                        class="flex items-center justify-center gap-1 rounded-full border border-pling-secondary/60 bg-white py-1.5 text-[10px] font-extrabold text-pling-secondary transition hover:bg-pling-secondary/10 active:scale-95">
                        <svg class="size-3.5" fill="currentColor"><use href="#i-download"/></svg>
                        .txt
                    </a>
                </div>
            </div>
        </div>

        {{-- WhatsApp --}}
        <a href="{{ $this->whatsappUrl }}" target="_blank" rel="noopener"
            class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-[#25D366] py-2.5 text-xs font-extrabold text-white shadow-md transition hover:brightness-105 active:scale-[0.99]">
            <svg class="size-5" fill="currentColor"><use href="#i-whatsapp"/></svg>
            Receive order status through WhatsApp
        </a>
    </div>

    <p class="mt-4 text-center text-[10px] font-semibold text-white/80">
        Powered by <span class="font-extrabold">PlingPager</span> · Status diperbarui otomatis
    </p>
</div>