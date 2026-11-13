<div>
    @if (! $isOnboarding)
        {{-- Settings View --}}
        <section class="w-full">
            @include('partials.settings-heading')

            <x-settings.layout :heading="__('Store Profile & Meta API')" :subheading="__('Manage your store details and WhatsApp Cloud API credentials')">
                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <div>
                        <flux:input
                            wire:model="store_name"
                            :label="__('Store Name')"
                            type="text"
                            required
                            placeholder="Kedai Kopi Pejuang"
                            :description="__('The display name for your store shown on customer pagers and receipts.')"
                        />
                        @error('store_name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <flux:separator />

                    <div class="space-y-4">
                        <flux:heading level="3" size="lg">{{ __('Meta WhatsApp Cloud API') }}</flux:heading>
                        <flux:text class="text-sm">
                            {{ __('Connect your WhatsApp Cloud API to send automated buzzer and order notifications to customers.') }}
                        </flux:text>

                        <div>
                            <flux:input
                                wire:model="meta_phone_number_id"
                                :label="__('Phone Number ID')"
                                type="text"
                                placeholder="104523984712390"
                            />
                            @error('meta_phone_number_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="meta_waba_id"
                                :label="__('WhatsApp Business Account (WABA) ID')"
                                type="text"
                                placeholder="108392019482910"
                            />
                            @error('meta_waba_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <flux:label>{{ __('Meta Permanent Access Token') }}</flux:label>
                                <button type="button" wire:click="toggleShowAccessToken" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $showAccessToken ? __('Hide Token') : __('Show Token') }}
                                </button>
                            </div>
                            <flux:input
                                wire:model="meta_access_token"
                                :type="$showAccessToken ? 'text' : 'password'"
                                placeholder="EAAG..."
                            />
                            @error('meta_access_token') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div class="pt-2">
                            <flux:button
                                type="button"
                                wire:click="testMetaConnection"
                                variant="subtle"
                                size="sm"
                                class="cursor-pointer"
                                :disabled="$isTestingConnection"
                            >
                                <span wire:loading.remove wire:target="testMetaConnection">{{ __('Test API Connection') }}</span>
                                <span wire:loading wire:target="testMetaConnection">{{ __('Testing...') }}</span>
                            </flux:button>
                        </div>

                        @if ($connectionStatus === 'success')
                            <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-sm text-emerald-700 dark:text-emerald-300">
                                <div class="flex items-center gap-2">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ $connectionMessage }}</span>
                                </div>
                            </div>
                        @elseif ($connectionStatus === 'error')
                            <div class="rounded-lg border border-rose-500/30 bg-rose-500/10 p-3 text-sm text-rose-700 dark:text-rose-300">
                                <div class="flex items-center gap-2">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>{{ $connectionMessage }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <flux:separator />

                    <div class="space-y-4">
                        <flux:heading level="3" size="lg">{{ __('Bank Account (Optional)') }}</flux:heading>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input
                                    wire:model="bank_name"
                                    :label="__('Bank Name')"
                                    type="text"
                                    placeholder="BCA / Mandiri / BRI / QRIS"
                                />
                            </div>
                            <div>
                                <flux:input
                                    wire:model="account_number"
                                    :label="__('Account Number')"
                                    type="text"
                                    placeholder="1234567890"
                                />
                            </div>
                        </div>

                        <div>
                            <flux:input
                                wire:model="account_owner_name"
                                :label="__('Account Holder Name')"
                                type="text"
                                placeholder="Nama Pemilik Rekening"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <flux:button variant="primary" type="submit">{{ __('Save Changes') }}</flux:button>
                    </div>
                </form>
            </x-settings.layout>
        </section>
    @else
        {{-- Onboarding Flow View --}}
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Langkah Setup Merchant PlingPager</span>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Setup Toko & WhatsApp API
                </h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto">
                    Konfigurasikan kedai Anda dan hubungkan Meta WhatsApp Cloud API agar pelanggan dapat menerima notifikasi buzzer otomatis.
                </p>
            </div>

            {{-- Step Tracker --}}
            <div class="grid grid-cols-3 gap-2 py-2">
                <button
                    type="button"
                    wire:click="setStep(1)"
                    class="flex flex-col items-center gap-1.5 p-2 rounded-lg transition-all text-center {{ $currentStep === 1 ? 'bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-500/30' : 'opacity-70 hover:opacity-100' }}"
                >
                    <div class="flex size-7 items-center justify-center rounded-full text-xs font-bold {{ $currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                        1
                    </div>
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Profil Toko</span>
                </button>

                <button
                    type="button"
                    wire:click="setStep(2)"
                    class="flex flex-col items-center gap-1.5 p-2 rounded-lg transition-all text-center {{ $currentStep === 2 ? 'bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-500/30' : 'opacity-70 hover:opacity-100' }}"
                >
                    <div class="flex size-7 items-center justify-center rounded-full text-xs font-bold {{ $currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                        2
                    </div>
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Meta API</span>
                </button>

                <button
                    type="button"
                    wire:click="setStep(3)"
                    class="flex flex-col items-center gap-1.5 p-2 rounded-lg transition-all text-center {{ $currentStep === 3 ? 'bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-500/30' : 'opacity-70 hover:opacity-100' }}"
                >
                    <div class="flex size-7 items-center justify-center rounded-full text-xs font-bold {{ $currentStep === 3 ? 'bg-indigo-600 text-white' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                        3
                    </div>
                    <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">Selesai</span>
                </button>
            </div>

            {{-- Step 1: Store Details --}}
            @if ($currentStep === 1)
                <div class="space-y-5">
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50 space-y-1.5">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                            <svg class="size-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Identitas Kedai / Toko
                        </h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                            Nama kedai ini akan tercantum pada struk digital dan halaman pager pelanggan ketika pesanan sedang diproses.
                        </p>
                    </div>

                    <div>
                        <flux:input
                            wire:model="store_name"
                            :label="__('Nama Toko / Kedai')"
                            type="text"
                            required
                            autofocus
                            placeholder="contoh: Kedai Kopi Pejuang"
                            :description="__('Beri nama yang mudah dikenali pelanggan Anda.')"
                        />
                        @error('store_name') <flux:error>{{ $message }}</flux:error> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <flux:button type="button" wire:click="skip" variant="ghost" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                            {{ __('Nanti saja, masuk ke Dashboard') }}
                        </flux:button>

                        <flux:button type="button" wire:click="nextStep" variant="primary">
                            {{ __('Lanjut ke Meta API') }} &rarr;
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- Step 2: Meta API Configuration --}}
            @if ($currentStep === 2)
                <div class="space-y-5">
                    <div class="rounded-xl border border-indigo-200/60 bg-indigo-50/50 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/20 space-y-2">
                        <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200 flex items-center gap-2">
                            <svg class="size-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Kunci WhatsApp Cloud API (Meta for Developers)
                        </h3>
                        <p class="text-xs text-indigo-800 dark:text-indigo-300">
                            PlingPager memanfaatkan WhatsApp Cloud API resmi dari Meta untuk mengirim notifikasi status makanan siap ke nomor WhatsApp pembeli secara instan.
                        </p>
                    </div>

                    {{-- Form Fields --}}
                    <div class="space-y-4">
                        <div>
                            <flux:input
                                wire:model="meta_phone_number_id"
                                :label="__('Meta Phone Number ID')"
                                type="text"
                                placeholder="contoh: 104523984712390"
                                :description="__('ID nomor telepon yang terdaftar di Meta WhatsApp API Setup.')"
                            />
                            @error('meta_phone_number_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="meta_waba_id"
                                :label="__('WhatsApp Business Account (WABA) ID')"
                                type="text"
                                placeholder="contoh: 108392019482910"
                                :description="__('ID akun WhatsApp Business Anda di dashboard Meta.')"
                            />
                            @error('meta_waba_id') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <flux:label>{{ __('Meta Permanent Access Token') }}</flux:label>
                                <button type="button" wire:click="toggleShowAccessToken" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $showAccessToken ? __('Sembunyikan') : __('Tampilkan Token') }}
                                </button>
                            </div>
                            <flux:input
                                wire:model="meta_access_token"
                                :type="$showAccessToken ? 'text' : 'password'"
                                placeholder="EAAG..."
                                :description="__('System User token dengan izin whatsapp_business_messaging.')"
                            />
                            @error('meta_access_token') <flux:error>{{ $message }}</flux:error> @enderror
                        </div>

                        {{-- Test Connection Button & Result --}}
                        <div class="pt-1">
                            <flux:button
                                type="button"
                                wire:click="testMetaConnection"
                                variant="subtle"
                                size="sm"
                                class="w-full cursor-pointer justify-center"
                                :disabled="$isTestingConnection"
                            >
                                <span wire:loading.remove wire:target="testMetaConnection" class="flex items-center gap-1.5">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Uji Koneksi Meta API
                                </span>
                                <span wire:loading wire:target="testMetaConnection">Menguji koneksi ke server Meta...</span>
                            </flux:button>
                        </div>

                        @if ($connectionStatus === 'success')
                            <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-sm text-emerald-700 dark:text-emerald-300">
                                <div class="flex items-center gap-2">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ $connectionMessage }}</span>
                                </div>
                            </div>
                        @elseif ($connectionStatus === 'error')
                            <div class="rounded-lg border border-rose-500/30 bg-rose-500/10 p-3 text-sm text-rose-700 dark:text-rose-300">
                                <div class="flex items-center gap-2">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>{{ $connectionMessage }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Helper Accordion --}}
                        <details class="group rounded-xl border border-zinc-200 p-3 text-xs dark:border-zinc-800">
                            <summary class="flex cursor-pointer items-center justify-between font-semibold text-zinc-700 dark:text-zinc-300">
                                <span>Panduan: Di mana menemukan Meta API Keys ini?</span>
                                <span class="transition group-open:rotate-180">&darr;</span>
                            </summary>
                            <div class="mt-2.5 space-y-1.5 text-zinc-600 dark:text-zinc-400 leading-relaxed border-t border-zinc-200 pt-2 dark:border-zinc-800">
                                <p>1. Buka portal resmi <a href="https://developers.facebook.com/apps" target="_blank" class="text-indigo-600 underline dark:text-indigo-400">Meta for Developers</a>.</p>
                                <p>2. Pilih Aplikasi Meta Anda, lalu klik menu <strong>WhatsApp &gt; API Setup</strong> di bilah sisi kiri.</p>
                                <p>3. Salin <strong>Phone Number ID</strong> dan <strong>WhatsApp Business Account ID</strong>.</p>
                                <p>4. Untuk <strong>Permanent Access Token</strong>, buat <em>System User</em> pada Meta Business Suite dengan permission <code>whatsapp_business_messaging</code> dan <code>whatsapp_business_management</code>.</p>
                            </div>
                        </details>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <flux:button type="button" wire:click="previousStep" variant="ghost">
                            &larr; {{ __('Kembali') }}
                        </flux:button>

                        <flux:button type="button" wire:click="nextStep" variant="primary">
                            {{ __('Lanjut') }} &rarr;
                        </flux:button>
                    </div>
                </div>
            @endif

            {{-- Step 3: Payment & Summary --}}
            @if ($currentStep === 3)
                <div class="space-y-5">
                    {{-- Summary Card --}}
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50 space-y-3">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Ringkasan Konfigurasi</h3>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between py-1 border-b border-zinc-200 dark:border-zinc-800">
                                <span class="text-zinc-500">Nama Toko</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $store_name ?: '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-200 dark:border-zinc-800">
                                <span class="text-zinc-500">Status WhatsApp API</span>
                                @if (!empty($meta_phone_number_id) && !empty($meta_access_token))
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                                        <svg class="size-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Kredensial Terisi
                                    </span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400 font-medium">Belum diisi (dapat dilengkapi nanti)</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Bank Details (Optional) --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <flux:heading level="4" size="base">{{ __('Informasi Rekening Pembayaran (Opsional)') }}</flux:heading>
                            <span class="text-xs text-zinc-500">Untuk transfer pembeli</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <flux:input
                                    wire:model="bank_name"
                                    :label="__('Nama Bank / Dompet')"
                                    type="text"
                                    placeholder="contoh: BCA / QRIS / Mandiri"
                                />
                            </div>
                            <div>
                                <flux:input
                                    wire:model="account_number"
                                    :label="__('Nomor Rekening')"
                                    type="text"
                                    placeholder="contoh: 1234567890"
                                />
                            </div>
                        </div>

                        <div>
                            <flux:input
                                wire:model="account_owner_name"
                                :label="__('Nama Pemilik Rekening')"
                                type="text"
                                placeholder="contoh: Kedai Pejuang"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <flux:button type="button" wire:click="previousStep" variant="ghost">
                            &larr; {{ __('Kembali') }}
                        </flux:button>

                        <div class="flex items-center gap-2">
                            <flux:button type="button" wire:click="skip" variant="ghost" class="text-zinc-500">
                                {{ __('Lewati') }}
                            </flux:button>
                            <flux:button type="button" wire:click="save" variant="primary">
                                {{ __('Simpan & Selesai') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
