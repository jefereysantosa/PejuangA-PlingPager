<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if (!auth()->user()->storeProfile?->meta_access_token)
            <div class="rounded-xl border border-amber-200/80 bg-amber-50/80 p-4 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400">
                            <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm">Hubungkan Meta WhatsApp Cloud API</h4>
                            <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                                Notifikasi buzzer WhatsApp ke pelanggan belum aktif. Masukkan Phone Number ID & Meta Access Token agar sistem pager bekerja secara otomatis.
                            </p>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <flux:button href="{{ route('store.setup') }}" variant="primary" size="sm" wire:navigate>
                            Setup WhatsApp API &rarr;
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts::app>
