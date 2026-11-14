<form wire:submit="save" class="space-y-4">
    {{-- Nama menu --}}
    <div>
        <label for="menu-name-{{ $this->getId() }}" class="mb-1 block text-xs font-bold text-pling-primary">Dish Name</label>
        <input id="menu-name-{{ $this->getId() }}" type="text" wire:model="name" placeholder="Ex: Nasi Goreng Pejuang"
            class="w-full rounded-full bg-zinc-200 px-4 py-2.5 text-sm text-pling-primary placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-pling-secondary/50">
        @error('name') <p class="mt-1 px-1 text-[11px] font-semibold text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori --}}
    <div>
        <label for="menu-category-{{ $this->getId() }}" class="mb-1 block text-xs font-bold text-pling-primary">Category</label>
        <div class="relative">
            <select id="menu-category-{{ $this->getId() }}" wire:model="category"
                class="w-full appearance-none rounded-full bg-zinc-200 px-4 py-2.5 pr-10 text-sm text-pling-primary outline-none transition focus:bg-white focus:ring-2 focus:ring-pling-secondary/50">
                <option value="meal">Meal</option>
                <option value="beverage">Beverage</option>
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 size-5 -translate-y-1/2 text-pling-secondary" fill="currentColor"><use href="#i-chevron-down"/></svg>
        </div>
        @error('category') <p class="mt-1 px-1 text-[11px] font-semibold text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Harga --}}
    <div>
        <label for="menu-price-{{ $this->getId() }}" class="mb-1 block text-xs font-bold text-pling-primary">Price</label>
        <div class="flex items-center gap-2">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-pling-accent text-[11px] font-extrabold text-white">RP</span>
            <input id="menu-price-{{ $this->getId() }}" type="number" inputmode="numeric" min="0" step="1" wire:model="price" placeholder="25000"
                class="w-full rounded-full bg-zinc-200 px-4 py-2.5 text-sm text-pling-primary placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-pling-secondary/50">
        </div>
        @if(is_numeric($price) && $price !== '')
            <p class="mt-1 px-1 text-[11px] font-semibold text-pling-secondary">Rp {{ number_format((int) $price, 0, ',', '.') }}</p>
        @endif
        @error('price') <p class="mt-1 px-1 text-[11px] font-semibold text-red-500">{{ $message }}</p> @enderror
    </div>

    {{-- Status ketersediaan --}}
    <div>
        <span class="mb-1 block text-xs font-bold text-pling-primary">Status</span>
        <div class="grid grid-cols-2 gap-1 rounded-full bg-zinc-200 p-1">
            <button type="button" wire:click="$set('isAvailable', true)"
                class="rounded-full py-1.5 text-xs font-bold transition {{ $isAvailable ? 'bg-pling-green text-pling-primary shadow' : 'text-zinc-400 hover:text-zinc-600' }}">
                Available
            </button>
            <button type="button" wire:click="$set('isAvailable', false)"
                class="rounded-full py-1.5 text-xs font-bold transition {{ ! $isAvailable ? 'bg-pling-accent text-white shadow' : 'text-zinc-400 hover:text-zinc-600' }}">
                Unavailable
            </button>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="menu-desc-{{ $this->getId() }}" class="mb-1 block text-xs font-bold text-pling-primary">Brief Description</label>
        <textarea id="menu-desc-{{ $this->getId() }}" wire:model="description" rows="3" maxlength="500"
            placeholder="Ex: Nasi goreng dengan ayam suwir dan telur mata sapi."
            class="w-full resize-none rounded-2xl bg-zinc-200 px-4 py-3 text-sm text-pling-primary placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-pling-secondary/50 pling-scroll"></textarea>
        <div class="mt-0.5 flex items-center justify-between px-1">
            @error('description') <p class="text-[11px] font-semibold text-red-500">{{ $message }}</p> @else <span></span> @enderror
            <span class="text-[10px] text-zinc-400">{{ strlen($description) }}/500</span>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="space-y-2 pt-1">
        <button type="submit" wire:loading.attr="disabled" wire:target="save"
            class="flex w-full items-center justify-center gap-2 rounded-full bg-pling-secondary py-2.5 text-sm font-extrabold text-white shadow-md transition hover:bg-pling-primary active:scale-[0.99] disabled:opacity-60">
            <svg wire:loading wire:target="save" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <span wire:loading.remove wire:target="save">{{ $this->isEdit ? 'Save' : 'Add Menu' }}</span>
            <span wire:loading wire:target="save">Menyimpan…</span>
        </button>

        @if($this->isEdit)
            <button type="button" wire:click="delete"
                onclick="return confirm('Hapus menu ini dari daftar?')"
                class="flex w-full items-center justify-center gap-2 rounded-full border border-red-300 bg-red-50 py-2.5 text-sm font-extrabold text-red-500 transition hover:bg-red-100 active:scale-[0.99]">
                <svg class="size-4" fill="currentColor"><use href="#i-delete"/></svg>
                Delete Menu
            </button>
        @endif
    </div>
</form>