<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[#155592] text-zinc-900 selection:bg-[#FA7268] selection:text-white relative overflow-x-hidden font-sans">
        {{-- Layered Abstract Background --}}
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" aria-hidden="true">
            <svg class="absolute inset-0 w-full h-full object-cover" viewBox="0 0 360 780" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Deep blue base -->
                <rect width="360" height="780" fill="#155592"/>

                <!-- Coral circle on top right -->
                <circle cx="370" cy="140" r="290" fill="#FA7268"/>

                <!-- Slate blue circle from top left -->
                <circle cx="20" cy="140" r="310" fill="#4A7295"/>

                <!-- Deep blue curve from bottom -->
                <circle cx="160" cy="840" r="460" fill="#155592"/>
            </svg>
        </div>

        <main class="min-h-screen flex items-center justify-center p-4 sm:p-6">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
