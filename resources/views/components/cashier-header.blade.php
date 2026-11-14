@props([
    'profile' => null,
    'user' => null,
])

@php
    $currentUser = $user ?? auth()->user();
    $storeName = $profile?->store_name ?? ($currentUser?->name ? $currentUser->name . "'s Store" : 'Buisness Name');
    $username = '@' . ($currentUser?->username ?? \Illuminate\Support\Str::slug($currentUser?->name ?? 'username', ''));
    $isOpen = (bool) ($profile?->is_open ?? false);
@endphp

<div>
    {{-- Header profil toko: Foto + Status & Nama Toko + Username --}}
    <div class="flex items-start gap-3.5">
        {{-- Foto Kotak & Badge Status --}}
        <div class="flex flex-col items-center shrink-0">
            <div class="relative flex size-18 sm:size-20 items-center justify-center rounded-2xl bg-zinc-200/90 border border-zinc-300/60 shadow-inner overflow-hidden text-zinc-500 font-semibold text-xs tracking-wide">
                <span class="select-none text-zinc-400">Photo</span>
            </div>
            <div class="mt-1.5">
                <span class="inline-block rounded-full px-3 py-0.5 text-[10px] sm:text-[11px] font-black tracking-wide shadow-xs transition-colors duration-200 {{ $isOpen ? 'bg-[#7BED8D] text-emerald-950' : 'bg-[#FA7268] text-white' }}">
                    {{ $isOpen ? 'Open' : 'Closed' }}
                </span>
            </div>
        </div>

        {{-- Detail Nama Toko & Username Kasir --}}
        <div class="min-w-0 flex-1 pt-1">
            <div class="flex items-center gap-1.5">
                <h1 class="truncate text-lg sm:text-xl font-extrabold tracking-tight text-[#155592]" title="{{ $storeName }}">
                    {{ $storeName }}
                </h1>
                <button type="button" 
                    wire:click="$dispatch('open-profile-editor')"
                    title="Edit profil toko & rekening"
                    class="flex size-7 shrink-0 items-center justify-center rounded-lg text-[#4A7295] transition hover:bg-zinc-200/80 hover:text-[#155592] active:scale-90"
                    aria-label="Edit Profile">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
            </div>
            <p class="truncate text-xs sm:text-sm font-semibold text-[#4A7295] mt-0.5">
                {{ $username }}
            </p>
        </div>
    </div>

    {{-- Slider / Tombol Toggle Buka & Tutup Toko --}}
    <button type="button" wire:click="toggleStore" wire:loading.attr="disabled"
        class="mt-4 flex w-full items-center justify-between rounded-full p-1 text-sm font-black shadow-sm transition active:scale-[0.99] {{ $isOpen ? 'bg-[#FA7268] text-white hover:bg-[#f86358]' : 'bg-[#7BED8D] text-[#155592] hover:bg-[#6ee680]' }}">
        @if ($isOpen)
            <span class="flex-1 text-center pl-8 tracking-wide">Close Store</span>
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#155592] text-white shadow-xs">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.59 18 19 16.59 14.42 12 19 7.41 17.59 6l-6 6z"/>
                    <path d="M11 18l1.41-1.41L7.83 12l4.58-4.59L11 6l-6 6z"/>
                </svg>
            </span>
        @else
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#155592] text-white shadow-xs">
                <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.41 6 5 7.41 9.58 12 5 16.59 6.41 18l6-6z"/>
                    <path d="M13 6l-1.41 1.41L16.17 12l-4.58 4.59L13 18l6-6z"/>
                </svg>
            </span>
            <span class="flex-1 text-center pr-8 tracking-wide">Open Store</span>
        @endif
    </button>

    {{-- Modal Pop-up Profile Editor --}}
    <livewire:cashier.profile-editor :show-trigger="false" />
</div>
