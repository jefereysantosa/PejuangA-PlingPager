<section class="w-full max-w-2xl mx-auto space-y-6">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    {{-- Logo Pill Header --}}
    <div class="flex justify-center">
        <div class="rounded-full bg-white px-6 py-2 shadow-sm border border-zinc-200/80">
            <x-pling-logo :size="22" text-class="text-base font-extrabold text-[#155592]" />
        </div>
    </div>

    {{-- Kartu Profil Toko & Status Kasir (sesuai Mockup PlingPager) --}}
    <div class="rounded-[28px] bg-[#FAF7F2] p-5 shadow-md border border-zinc-200/70">
        <div class="flex items-start gap-4">
            {{-- Foto Kotak & Status --}}
            <div class="flex flex-col items-center shrink-0">
                <div class="relative flex size-20 items-center justify-center rounded-2xl bg-zinc-200/90 border border-zinc-300/60 shadow-inner overflow-hidden text-zinc-400 font-semibold text-xs tracking-wide">
                    <span class="select-none">Photo</span>
                </div>
                <div class="mt-1.5">
                    <span class="inline-block rounded-full px-3 py-0.5 text-[11px] font-black tracking-wide shadow-xs transition-colors duration-200 {{ $is_open ? 'bg-[#7BED8D] text-emerald-950' : 'bg-[#FA7268] text-white' }}">
                        {{ $is_open ? 'Open' : 'Closed' }}
                    </span>
                </div>
            </div>

            {{-- Nama Toko & Username --}}
            <div class="min-w-0 flex-1 pt-1">
                <div class="flex items-center gap-1.5">
                    <h1 class="truncate text-xl font-extrabold tracking-tight text-[#155592]">
                        {{ $store_name ?: (auth()->user()->name . "'s Store") }}
                    </h1>
                    <button type="button" 
                        wire:click="$dispatch('open-profile-editor')"
                        title="Edit Profil Cepat"
                        class="flex size-7 shrink-0 items-center justify-center rounded-lg text-[#4A7295] transition hover:bg-zinc-200/80 hover:text-[#155592] active:scale-90"
                        aria-label="Edit Profile">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                </div>
                <p class="truncate text-sm font-semibold text-[#4A7295] mt-0.5">
                    {{ '@' . (auth()->user()->username ?? \Illuminate\Support\Str::slug(auth()->user()->name, '')) ?: auth()->user()->name }}
                </p>
                <div class="mt-2 flex flex-wrap gap-2 text-xs text-zinc-500">
                    <span class="inline-flex items-center gap-1 rounded-md bg-zinc-200/70 px-2 py-0.5">
                        <svg class="size-3.5 text-[#155592]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $email }}
                    </span>
                    @if($bank_name && $account_number)
                        <span class="inline-flex items-center gap-1 rounded-md bg-zinc-200/70 px-2 py-0.5">
                            <svg class="size-3.5 text-[#155592]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            {{ $bank_name }} - {{ $account_number }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Toggle Buka/Tutup Toko --}}
        <button type="button" wire:click="toggleStore" wire:loading.attr="disabled"
            class="mt-4 flex w-full items-center justify-between rounded-full p-1 text-sm font-black shadow-sm transition active:scale-[0.99] {{ $is_open ? 'bg-[#FA7268] text-white hover:bg-[#f86358]' : 'bg-[#7BED8D] text-[#155592] hover:bg-[#6ee680]' }}">
            @if ($is_open)
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
    </div>

    {{-- Form Edit Data Profil Lengkap --}}
    <div class="rounded-[28px] bg-[#FAF7F2] p-6 shadow-md border border-zinc-200/70 space-y-6">
        <div>
            <h3 class="text-lg font-extrabold text-[#155592]">{{ __('Informasi Akun & Toko') }}</h3>
            <p class="text-xs text-[#4A7295] mt-0.5">{{ __('Perbarui nama pengguna, nama toko, email, dan rekening bank.') }}</p>
        </div>

        <form wire:submit="updateProfileInformation" class="space-y-5">
            {{-- Nama & Email Akun --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#155592] mb-1">{{ __('Nama Pengguna / Kasir') }}</label>
                    <input type="text" wire:model="name" required autofocus autocomplete="name"
                        class="w-full rounded-full bg-zinc-200/90 px-4 py-2.5 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                    @error('name') <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#155592] mb-1">{{ __('Email') }}</label>
                    <input type="email" wire:model="email" required autocomplete="email"
                        class="w-full rounded-full bg-zinc-200/90 px-4 py-2.5 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                    @error('email') <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($this->hasUnverifiedEmail)
                <div class="rounded-xl bg-amber-50 p-3 text-xs text-amber-800 border border-amber-200">
                    {{ __('Your email address is unverified.') }}
                    <button type="button" class="font-bold underline ml-1 cursor-pointer" wire:click.prevent="resendVerificationNotification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </div>
            @endif

            <hr class="border-zinc-200" />

            {{-- Nama Toko --}}
            <div>
                <label class="block text-xs font-bold text-[#155592] mb-1">{{ __('Nama Toko / Kedai') }}</label>
                <input type="text" wire:model="store_name" placeholder="contoh: Kedai Kopi Pejuang"
                    class="w-full rounded-full bg-zinc-200/90 px-4 py-2.5 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                @error('store_name') <p class="text-[11px] font-semibold text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Rekening Pembayaran --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-[#155592]">{{ __('Rekening Pembayaran / Transfer') }}</label>
                    <span class="text-[11px] text-zinc-400">Untuk info ke pembeli</span>
                </div>

                {{-- Preset Bank Buttons --}}
                <div class="flex flex-wrap gap-1.5">
                    @foreach (['BCA', 'Mandiri', 'BRI', 'BNI', 'BSI', 'QRIS'] as $preset)
                        <button type="button" wire:click="$set('bank_name', '{{ $preset }}')"
                            class="rounded-full px-3 py-1 text-xs font-bold transition {{ $bank_name === $preset ? 'bg-[#155592] text-white shadow-xs' : 'bg-zinc-200 text-zinc-600 hover:bg-zinc-300' }}">
                            {{ $preset }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-500 mb-1">{{ __('Nama Bank / E-Wallet') }}</label>
                        <input type="text" wire:model="bank_name" placeholder="BCA / Mandiri / QRIS"
                            class="w-full rounded-full bg-zinc-200/90 px-4 py-2 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-500 mb-1">{{ __('Nomor Rekening') }}</label>
                        <input type="text" wire:model="account_number" placeholder="1234567890"
                            class="w-full rounded-full bg-zinc-200/90 px-4 py-2 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-zinc-500 mb-1">{{ __('Atas Nama') }}</label>
                        <input type="text" wire:model="account_owner_name" placeholder="Nama Pemilik Rekening"
                            class="w-full rounded-full bg-zinc-200/90 px-4 py-2 text-sm text-[#155592] placeholder-zinc-400 outline-none transition focus:bg-white focus:ring-2 focus:ring-[#4A7295]/50">
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan & Tautan Aksi --}}
            <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button type="submit"
                    class="w-full sm:w-auto rounded-full bg-[#155592] px-8 py-2.5 text-sm font-extrabold text-white shadow-md transition hover:bg-[#0f4477] active:scale-95">
                    {{ __('Simpan Perubahan') }}
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cashier.menu') }}" class="text-xs font-bold text-[#155592] hover:underline">
                        &larr; {{ __('Menu Kasir') }}
                    </a>
                    <span class="text-zinc-300">•</span>
                    <a href="{{ route('store.edit') }}" class="text-xs font-bold text-[#4A7295] hover:underline">
                        {{ __('Setup Meta WhatsApp') }} &rarr;
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal Pop-up Profile Editor untuk Quick Edit --}}
    <livewire:cashier.profile-editor :show-trigger="false" />

    @if ($this->showDeleteUser)
        <div class="rounded-[28px] bg-red-50/50 p-6 border border-red-200/60 shadow-xs">
            <livewire:settings.delete-user-form />
        </div>
    @endif
</section>
