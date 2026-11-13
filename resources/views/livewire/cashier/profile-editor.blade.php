<div>
    {{-- Trigger Button (optional) --}}
    @if ($showTrigger)
        <flux:button
            wire:click="open"
            variant="subtle"
            size="sm"
            icon="building-storefront"
            class="cursor-pointer"
        >
            {{ __('Edit Profil & Rekening') }}
        </flux:button>
    @endif

    {{-- Pop-up Modal --}}
    <flux:modal
        name="cashier-profile-editor-modal"
        wire:model="showModal"
        class="max-w-xl md:min-w-[32rem]"
        @close="close"
    >
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <flux:heading size="lg">{{ __('Edit Profil & Rekening Kasir') }}</flux:heading>
                    <flux:subheading>
                        {{ __('Perbarui data kedai dan detail rekening bank untuk penerimaan pembayaran pelanggan.') }}
                    </flux:subheading>
                </div>
            </div>

            <form wire:submit="save" class="space-y-5">
                {{-- Store & Cashier Details --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        <span>{{ __('Informasi Toko & Kasir') }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input
                                wire:model="store_name"
                                :label="__('Nama Toko / Kedai')"
                                type="text"
                                required
                                placeholder="contoh: Kedai Kopi Pejuang"
                            />
                            @error('store_name') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="name"
                                :label="__('Nama Kasir / Pemilik')"
                                type="text"
                                required
                                placeholder="contoh: Budi Santoso"
                            />
                            @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>
                </div>

                <flux:separator />

                {{-- Bank Account Details --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Rekening Pembayaran / Transfer') }}
                        </span>
                        <span class="text-xs text-zinc-400">Untuk info pembayaran pelanggan</span>
                    </div>

                    {{-- Quick Bank Selection Pills --}}
                    <div>
                        <flux:label class="mb-1.5 text-xs">{{ __('Pilihan Cepat:') }}</flux:label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (['BCA', 'Mandiri', 'BRI', 'BNI', 'BSI', 'QRIS'] as $preset)
                                <button
                                    type="button"
                                    wire:click="selectBank('{{ $preset }}')"
                                    class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium transition-all {{ $bank_name === $preset ? 'bg-indigo-600 text-white shadow-xs' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}"
                                >
                                    {{ $preset }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input
                                wire:model="bank_name"
                                :label="__('Nama Bank / E-Wallet')"
                                type="text"
                                placeholder="BCA / Mandiri / QRIS"
                            />
                            @error('bank_name') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="account_number"
                                :label="__('Nomor Rekening / ID QRIS')"
                                type="text"
                                placeholder="contoh: 1234567890"
                            />
                            @error('account_number') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>
                    </div>

                    <div>
                        <flux:input
                            wire:model="account_owner_name"
                            :label="__('Nama Pemilik Rekening (Atas Nama)')"
                            type="text"
                            placeholder="contoh: Kedai Kopi Pejuang"
                        />
                        @error('account_owner_name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>
                </div>

                {{-- WhatsApp API Quick Status --}}
                <div class="rounded-xl border border-zinc-200 bg-zinc-50/60 p-3 text-xs dark:border-zinc-800 dark:bg-zinc-900/60 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex size-2 rounded-full {{ $isMetaConnected ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        <span class="text-zinc-600 dark:text-zinc-400">
                            Status WhatsApp Cloud API:
                            <strong class="{{ $isMetaConnected ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $isMetaConnected ? 'Terhubung' : 'Belum Terhubung' }}
                            </strong>
                        </span>
                    </div>
                    <flux:link :href="route('store.edit')" class="text-xs cursor-pointer" wire:navigate>
                        {{ __('Atur API Meta') }} &rarr;
                    </flux:link>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-3">
                    <flux:button variant="ghost" type="button" wire:click="close">
                        {{ __('Batal') }}
                    </flux:button>

                    <flux:button variant="primary" type="submit">
                        {{ __('Simpan Perubahan') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
