<x-layouts::auth.pling :title="__('Log in')">
    <div class="w-full max-w-[340px] sm:max-w-[370px] rounded-[30px] sm:rounded-[34px] bg-[#FAF9F5] px-7 py-8 sm:px-8 sm:py-9 shadow-2xl shadow-blue-950/25 transition-all">
        {{-- Logo & Brand --}}
        <div class="flex items-center justify-center gap-2 mb-4">
            <svg class="size-7 shrink-0" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Left outer arc -->
                <path d="M8.5 8.5C5.8 11.2 4.2 14.5 4.2 18C4.2 21.5 5.8 24.8 8.5 27.5" stroke="#255D8E" stroke-width="2.6" stroke-linecap="round"/>
                <!-- Right outer arc -->
                <path d="M27.5 8.5C30.2 11.2 31.8 14.5 31.8 18C31.8 21.5 30.2 24.8 27.5 27.5" stroke="#255D8E" stroke-width="2.6" stroke-linecap="round"/>
                <!-- Left inner arc -->
                <path d="M12.8 12.8C11.2 14.3 10.2 16.1 10.2 18C10.2 19.9 11.2 21.7 12.8 23.2" stroke="#255D8E" stroke-width="2.6" stroke-linecap="round"/>
                <!-- Right inner arc -->
                <path d="M23.2 12.8C24.8 14.3 25.8 16.1 25.8 18C25.8 19.9 24.8 21.7 23.2 23.2" stroke="#255D8E" stroke-width="2.6" stroke-linecap="round"/>
                <!-- Center transmitter dot -->
                <circle cx="18" cy="18" r="3.2" fill="#FA7368"/>
            </svg>
            <span class="text-[22px] sm:text-[23px] font-semibold text-[#2D455E] tracking-tight">PlingPager</span>
        </div>

        {{-- Welcome Heading --}}
        <h1 class="text-center text-[22px] sm:text-[24px] font-bold text-[#16589B] tracking-tight mb-6">
            Welcome!
        </h1>

        {{-- Session Status --}}
        <x-auth-session-status class="mb-4 text-center text-xs" :status="session('status')" />

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-rose-50 p-2.5 text-center text-xs text-rose-600 border border-rose-200/60">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.store') }}" class="space-y-3.5">
            @csrf

            {{-- E-Mail Field --}}
            <div class="relative flex items-center bg-[#E5E6E8] rounded-full px-4 py-2.5 transition-all focus-within:ring-2 focus-within:ring-[#16589B]/40 focus-within:bg-[#EDEDED]">
                <svg class="size-4 text-[#8D97A0] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="E-Mail"
                    class="w-full bg-transparent pl-3 pr-2 text-xs sm:text-sm text-zinc-800 placeholder-[#8D97A0] focus:outline-none"
                />
            </div>

            {{-- Password Field --}}
            <div>
                <div x-data="{ show: false }" class="relative flex items-center bg-[#E5E6E8] rounded-full px-4 py-2.5 transition-all focus-within:ring-2 focus-within:ring-[#16589B]/40 focus-within:bg-[#EDEDED]">
                    <svg class="size-4 text-[#8D97A0] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <input
                        id="password"
                        name="password"
                        :type="show ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        class="w-full bg-transparent pl-3 pr-8 text-xs sm:text-sm text-zinc-800 placeholder-[#8D97A0] focus:outline-none"
                    />
                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute right-3.5 text-[#8D97A0] hover:text-[#5580A5] transition-colors focus:outline-none cursor-pointer"
                        tabindex="-1"
                        aria-label="Toggle password visibility"
                    >
                        <svg x-show="!show" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" x-cloak class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                {{-- Forgot Password Link --}}
                @if (Route::has('password.request'))
                    <div class="flex justify-end pt-1.5 pr-1">
                        <a href="{{ route('password.request') }}" class="text-[11px] sm:text-xs text-[#5580A5] hover:text-[#16589B] transition-colors font-normal" wire:navigate>
                            Forgot Password?
                        </a>
                    </div>
                @endif
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-center pt-2">
                <button
                    type="submit"
                    class="rounded-full bg-[#487295] hover:bg-[#3d6382] active:scale-95 text-white text-xs sm:text-sm font-medium px-8 py-2.5 shadow-sm transition-all cursor-pointer min-w-[120px]"
                    data-test="login-button"
                >
                    Sign In
                </button>
            </div>
        </form>

        {{-- "Or" Divider --}}
        <div class="text-center text-xs text-[#526374] my-2 font-normal">
            Or
        </div>

        {{-- Sign in with Google --}}
        <div class="flex justify-center">
            <button
                type="button"
                class="rounded-full border border-dashed border-gray-300 bg-white px-5 py-2 flex items-center justify-center gap-2.5 shadow-xs hover:bg-gray-50 active:scale-95 transition-all cursor-pointer"
            >
                <svg class="size-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                </svg>
                <span class="text-xs font-medium text-gray-700">Sign in with Google</span>
            </button>
        </div>

        {{-- Footer --}}
        <div class="text-center text-[11px] sm:text-xs text-gray-700 mt-5">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-bold text-[#16589B] hover:underline" wire:navigate>
                Sign Up
            </a>
        </div>
    </div>
</x-layouts::auth.pling>
