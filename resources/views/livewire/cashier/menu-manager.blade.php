<div>
    {{-- Logo pill --}}
    <div class="mb-4 flex justify-center">
        <div class="rounded-full bg-pling-neutral px-6 py-2 shadow-md">
            <x-pling-logo :size="24" text-class="text-base" />
        </div>
    </div>

    {{-- Kartu utama --}}
    <div class="rounded-[20px] bg-pling-neutral p-4 shadow-xl">
        {{-- Header toko --}}
        <div class="flex items-start gap-3">
            <div class="relative shrink-0">
                <div class="flex size-14 items-center justify-center rounded-xl bg-zinc-200 text-xl font-extrabold text-pling-secondary">
                    {{ strtoupper(substr($profile?->store_name ?? auth()->user()->name, 0, 1)) }}
                </div>
                <span class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full px-2 py-0.5 text-[10px] font-bold {{ $profile?->is_open ? 'bg-pling-green text-pling-primary' : 'bg-pling-accent text-white' }}">
                    {{ $profile?->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>
            <div class="min-w-0 flex-1 pt-0.5">
                <a href="{{ route('store.edit') }}" class="group flex items-center gap-1 text-lg font-extrabold leading-tight text-pling-primary">
                    <span class="truncate">{{ $profile?->store_name ?? auth()->user()->name }}</span>
                    <svg class="size-4 shrink-0 text-pling-secondary transition group-hover:text-pling-primary" fill="currentColor"><use href="#i-edit"/></svg>
                </a>
                <p class="truncate text-xs font-semibold text-pling-secondary">{{ auth()->user()->name }}</p>
            </div>
        </div>

        {{-- Toggle buka/tutup toko --}}
        <button type="button" wire:click="toggleStore" wire:loading.attr="disabled"
            class="mt-4 flex w-full items-center gap-2 rounded-full p-1 pr-4 text-sm font-extrabold shadow-sm transition active:scale-[0.99] {{ $profile?->is_open ? 'bg-pling-accent text-white' : 'bg-pling-green text-pling-primary' }}">
            <span class="flex size-8 items-center justify-center rounded-full bg-pling-secondary text-white">
                <svg class="size-5 transition-transform duration-300 {{ $profile?->is_open ? 'rotate-180' : '' }}" fill="currentColor"><use href="#i-double-arrow"/></svg>
            </span>
            <span class="flex-1 text-center">{{ $profile?->is_open ? 'Close Store' : 'Open Store' }}</span>
        </button>

        {{-- Tabs --}}
        <div class="mt-3 grid grid-cols-2 gap-1 rounded-full bg-zinc-200 p-1">
            <button type="button" wire:click="$set('activeTab', 'settings')"
                class="rounded-full py-1.5 text-xs font-bold transition {{ $activeTab === 'settings' ? 'bg-pling-secondary text-white shadow' : 'text-zinc-500 hover:text-pling-primary' }}">
                Menu Setting
            </button>
            <button type="button" wire:click="$set('activeTab', 'add')"
                class="rounded-full py-1.5 text-xs font-bold transition {{ $activeTab === 'add' ? 'bg-pling-secondary text-white shadow' : 'text-zinc-500 hover:text-pling-primary' }}">
                Add Menu
            </button>
        </div>

        @if($activeTab === 'settings')
            {{-- Pencarian --}}
            <div class="relative mt-3">
                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" fill="currentColor"><use href="#i-search"/></svg>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search"
                    class="w-full rounded-full bg-zinc-200 py-2 pl-9 pr-4 text-sm text-pling-primary placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-pling-secondary/50">
            </div>

            {{-- Filter & urutkan --}}
            <div class="mt-2 flex items-center gap-1.5 overflow-x-auto pb-1 pling-scroll">
                @foreach(['all' => 'All', 'meal' => 'Meal', 'beverage' => 'Beverages'] as $value => $label)
                    <button type="button" wire:click="$set('categoryFilter', '{{ $value }}')"
                        class="shrink-0 rounded-full px-3 py-1 text-[11px] font-bold transition {{ $categoryFilter === $value ? 'bg-pling-primary text-white' : 'bg-zinc-200 text-zinc-500 hover:bg-zinc-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
                <span class="mx-0.5 h-4 w-px shrink-0 bg-zinc-300"></span>
                @foreach(['all' => 'Any status', 'available' => 'Available', 'unavailable' => 'Unavailable'] as $value => $label)
                    <button type="button" wire:click="$set('availabilityFilter', '{{ $value }}')"
                        class="shrink-0 rounded-full px-3 py-1 text-[11px] font-bold transition {{ $availabilityFilter === $value ? 'bg-pling-secondary text-white' : 'bg-zinc-200 text-zinc-500 hover:bg-zinc-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="mt-1 flex items-center justify-between">
                <p class="text-[11px] font-semibold text-zinc-400">{{ $meals->count() + $beverages->count() }} item</p>
                <div class="relative">
                    <select wire:model.live="sortBy"
                        class="appearance-none rounded-full bg-zinc-200 py-1 pl-3 pr-7 text-[11px] font-bold text-pling-secondary outline-none focus:ring-2 focus:ring-pling-secondary/50">
                        <option value="latest">Newest</option>
                        <option value="name">Name A–Z</option>
                        <option value="price_low">Price: Low → High</option>
                        <option value="price_high">Price: High → Low</option>
                    </select>
                    <svg class="pointer-events-none absolute right-2 top-1/2 size-3.5 -translate-y-1/2 text-pling-secondary" fill="currentColor"><use href="#i-chevron-down"/></svg>
                </div>
            </div>

            {{-- Daftar menu per kategori --}}
            @foreach(['meal' => ['label' => 'Meal', 'icon' => 'i-restaurant', 'items' => $meals], 'beverage' => ['label' => 'Beverages', 'icon' => 'i-cafe', 'items' => $beverages]] as $catKey => $section)
                @if($section['items']->count() > 0)
                    <div class="mt-4">
                        <div class="mb-2 flex items-center gap-1.5">
                            <svg class="size-4 text-pling-secondary" fill="currentColor"><use href="#{{ $section['icon'] }}"/></svg>
                            <h2 class="text-base font-extrabold text-pling-primary">{{ $section['label'] }}</h2>
                            <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-[10px] font-bold text-zinc-500">{{ $section['items']->count() }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($section['items'] as $index => $product)
                                <div wire:key="product-{{ $product->id }}"
                                    class="flex items-center gap-2 rounded-2xl border border-pling-secondary/40 bg-white/70 p-3 transition {{ $product->is_available ? '' : 'opacity-70' }}">
                                    <span class="w-5 shrink-0 text-center text-xs font-extrabold text-pling-secondary">{{ $index + 1 }}.</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-extrabold text-pling-primary">{{ $product->name }}</p>
                                        <p class="text-[11px] font-semibold text-pling-secondary">Price : Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @if($product->description)
                                            <p class="mt-0.5 line-clamp-1 text-[10px] text-zinc-400">{{ $product->description }}</p>
                                        @endif
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-zinc-400">Status:</span>
                                            <button type="button" wire:click="toggleAvailability({{ $product->id }})" wire:loading.attr="disabled"
                                                title="Tap untuk mengubah ketersediaan"
                                                class="rounded-full px-2 py-0.5 text-[10px] font-bold transition active:scale-95 {{ $product->is_available ? 'bg-pling-green text-pling-primary' : 'bg-pling-accent text-white' }}">
                                                {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="openEditSheet({{ $product->id }})"
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full text-pling-secondary transition hover:bg-pling-secondary/10 hover:text-pling-primary active:scale-90"
                                        aria-label="Edit {{ $product->name }}">
                                        <svg class="size-5" fill="currentColor"><use href="#i-settings"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if($meals->count() === 0 && $beverages->count() === 0)
                <div class="mt-4 rounded-2xl border-2 border-dashed border-pling-secondary/40 p-6 text-center">
                    <svg class="mx-auto size-8 text-pling-secondary/60" fill="currentColor"><use href="#i-restaurant"/></svg>
                    <p class="mt-2 text-sm font-bold text-pling-primary">
                        {{ $search !== '' || $categoryFilter !== 'all' || $availabilityFilter !== 'all' ? 'Tidak ada menu yang cocok.' : 'Belum ada menu.' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">
                        {{ $search !== '' || $categoryFilter !== 'all' || $availabilityFilter !== 'all' ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan makanan & minuman pertama Anda.' }}
                    </p>
                    <button type="button" wire:click="$set('activeTab', 'add')"
                        class="mt-3 inline-flex items-center gap-1 rounded-full bg-pling-secondary px-4 py-1.5 text-xs font-bold text-white transition hover:bg-pling-primary active:scale-95">
                        <svg class="size-4" fill="currentColor"><use href="#i-add"/></svg>
                        Add Menu
                    </button>
                </div>
            @endif
        @else
            <div class="mt-4">
                <livewire:cashier.menu-form />
            </div>
        @endif
    </div>

    {{-- Bottom sheet Edit Menu --}}
    @if($showEditSheet && $editingProductId)
        <div class="fixed inset-0 z-50 flex items-end justify-center" role="dialog" aria-modal="true" aria-label="Edit Menu">
            <div class="absolute inset-0 bg-pling-primary/40 backdrop-blur-[2px]" wire:click="closeEditSheet"></div>
            <div class="relative z-10 max-h-[88dvh] w-full max-w-md overflow-y-auto rounded-t-[24px] bg-pling-neutral px-5 pb-8 pt-3 shadow-2xl animate-sheet-up pling-scroll">
                <div class="mx-auto mb-3 h-1 w-12 rounded-full bg-zinc-300"></div>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-pling-primary">Edit Menu</h2>
                    <button type="button" wire:click="closeEditSheet"
                        class="flex size-8 items-center justify-center rounded-full bg-zinc-200 text-zinc-500 transition hover:bg-zinc-300 active:scale-90" aria-label="Tutup">
                        <svg class="size-4" fill="currentColor"><use href="#i-close"/></svg>
                    </button>
                </div>
                <livewire:cashier.menu-form :product-id="$editingProductId" :key="'edit-'.$editingProductId" />
            </div>
        </div>
    @endif

    {{-- FAB navigasi bawah (Orders / POS / Menu / Consumer Pager) --}}
    <div x-data="{ open: false }" class="fixed bottom-5 right-5 z-40 flex flex-col items-end gap-2 sm:right-[calc(50%-13rem)]" @keydown.escape.window="open = false">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-3"
            class="flex flex-col items-end gap-2" x-cloak>
            <a href="{{ $latestOrderCode ? route('consumer.pager', $latestOrderCode) : route('cashier.orders') }}" target="_blank"
                class="flex items-center gap-2 rounded-full bg-pling-neutral px-4 py-2 text-xs font-extrabold text-pling-primary shadow-lg transition hover:bg-white active:scale-95">
                <svg class="size-4 text-pling-secondary" fill="currentColor"><use href="#i-phone"/></svg>
                Consumer Pager
            </a>
            <a href="{{ route('cashier.orders') }}"
                class="flex items-center gap-2 rounded-full bg-pling-neutral px-4 py-2 text-xs font-extrabold text-pling-primary shadow-lg transition hover:bg-white active:scale-95">
                <svg class="size-4 text-pling-secondary" fill="currentColor"><use href="#i-list"/></svg>
                Orders
            </a>
            <a href="{{ route('cashier.pos') }}"
                class="flex items-center gap-2 rounded-full bg-pling-neutral px-4 py-2 text-xs font-extrabold text-pling-primary shadow-lg transition hover:bg-white active:scale-95">
                <svg class="size-4 text-pling-secondary" fill="currentColor"><use href="#i-cart"/></svg>
                POS Kasir
            </a>
            <button type="button" @click="open = false"
                class="flex items-center gap-2 rounded-full bg-pling-primary px-4 py-2 text-xs font-extrabold text-white shadow-lg transition active:scale-95">
                <svg class="size-4" fill="currentColor"><use href="#i-restaurant"/></svg>
                Menu
            </button>
        </div>
        <button type="button" @click="open = !open"
            class="flex size-14 items-center justify-center rounded-full bg-pling-neutral shadow-xl ring-2 ring-pling-secondary/50 transition hover:scale-105 active:scale-95"
            :aria-expanded="open" aria-label="Navigasi">
            <x-pling-logo :size="34" :with-text="false" />
        </button>
    </div>
</div>