<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Receipt {{ $order->order_code }} — PlingPager</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .receipt { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-100 py-8 text-pling-primary">
    <div class="mx-auto max-w-sm px-4">
        <div class="no-print mb-4 flex gap-2">
            <button onclick="window.print()"
                class="flex-1 rounded-full bg-pling-primary py-2.5 text-sm font-extrabold text-white transition hover:brightness-110 active:scale-[0.99]">
                🖨️ Print / Save as PDF
            </button>
            <a href="{{ route('consumer.pager.receipt.txt', $order->order_code) }}"
                class="flex items-center rounded-full border border-pling-secondary bg-white px-4 py-2.5 text-sm font-extrabold text-pling-secondary transition hover:bg-pling-secondary/10">
                .txt
            </a>
        </div>

        <div class="receipt rounded-[20px] bg-white p-6 shadow-xl">
            <div class="text-center">
                <p class="text-lg font-extrabold tracking-tight text-pling-primary">PlingPager</p>
                <p class="mt-1 text-base font-extrabold">{{ $storeProfile?->store_name ?? 'PlingPager Store' }}</p>
                <p class="text-xs text-pling-secondary">E-Receipt · {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            <div class="my-4 border-t border-dashed border-zinc-300"></div>

            <div class="space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-zinc-500">Recipient</span><span class="font-bold">{{ $order->customer_name }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500">Order ID</span><span class="font-bold">{{ $order->order_code }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500">Status</span><span class="font-bold capitalize">{{ $order->status }}</span></div>
            </div>

            <div class="my-4 border-t border-dashed border-zinc-300"></div>

            <div class="space-y-2 text-sm">
                @foreach($order->orderItems as $item)
                    <div>
                        <div class="flex justify-between font-bold">
                            <span>{{ $item->product?->name ?? 'Menu dihapus' }}</span>
                            <span>{{ number_format($item->price_at_time * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-zinc-500">{{ $item->quantity }} × Rp {{ number_format($item->price_at_time, 0, ',', '.') }}</p>
                    </div>
                @endforeach
                @if($order->notes)
                    <p class="text-xs italic text-zinc-500">Note: {{ $order->notes }}</p>
                @endif
            </div>

            <div class="my-4 border-t border-dashed border-zinc-300"></div>

            <div class="space-y-1 text-sm">
                <div class="flex justify-between text-zinc-500"><span>Subtotal</span><span>{{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-zinc-500"><span>Platform Fee</span><span>{{ number_format($platformFee, 0, ',', '.') }}</span></div>
                <div class="mt-2 flex justify-between border-t border-zinc-200 pt-2 text-base font-extrabold">
                    <span>Total</span><span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <p class="mt-6 text-center text-[10px] text-zinc-400">
                Track live: {{ route('consumer.pager', $order->order_code) }}<br>
                Powered by PlingPager
            </p>
        </div>

        <div class="no-print mt-4 text-center">
            <a href="{{ route('consumer.pager', $order->order_code) }}" class="text-xs font-bold text-pling-secondary underline">← Kembali ke Pager</a>
        </div>
    </div>
</body>
</html>