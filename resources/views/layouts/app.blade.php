<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('img/logosiprisda.png') }}">
        <style>[x-cloak]{display:none;}</style>
        @include('layouts.partials.brand-theme')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://kit.fontawesome.com/a2e0e9b7b3.js" crossorigin="anonymous" defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-[#e7f5ff] via-white to-[#eaf9f3] text-slate-900 overflow-x-hidden">
        <div class="min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: false }">

            <div class="md:flex">
                <!-- Desktop sidebar (fixed) -->
                <div class="hidden md:block">
                    @auth
                        @php($role = auth()->user()->role)
                        @if($role === 'superadmin')
                            @include('layouts.partials.sidebar-superadmin')
                        @elseif($role === 'admin')
                            @include('layouts.partials.sidebar-admin')
                        @elseif($role === 'kesbangpol')
                            @include('layouts.partials.sidebar-kesbangpol')
                        @else
                            @include('layouts.partials.sidebar-user')
                        @endif
                    @endauth
                </div>

                <!-- Main content area -->
                <div class="flex-1 min-h-screen flex flex-col md:ml-64">
                    @include('layouts.navigation')

                    @isset($header)
                        <header class="bg-white/90 backdrop-blur border-b border-orange-100 shadow-sm">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="flex-1">
                        <div class="px-4 sm:px-6 lg:px-10 py-8 space-y-6">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>

            @if(session('success') || session('error'))
                <div class="fixed right-6 bottom-6 z-50 space-y-3">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-cloak x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                             class="flex items-start gap-3 rounded-xl bg-emerald-600 text-white shadow-xl shadow-emerald-200/40 px-4 py-3">
                            <div class="mt-0.5"><i class="fas fa-circle-check"></i></div>
                            <div class="text-sm font-medium leading-relaxed">{{ session('success') }}</div>
                            <button @click="show = false" class="ml-2 text-white/80 hover:text-white">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div x-data="{ show: true }" x-cloak x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                             class="flex items-start gap-3 rounded-xl bg-rose-600 text-white shadow-xl shadow-rose-200/40 px-4 py-3">
                            <div class="mt-0.5"><i class="fas fa-circle-exclamation"></i></div>
                            <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                            <button @click="show = false" class="ml-2 text-white/80 hover:text-white">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 md:hidden" aria-hidden="true">
            <div @click="sidebarOpen = false" class="fixed inset-0 bg-black/50"></div>
            <div class="fixed inset-y-0 left-0 w-64 transform transition-transform duration-200 bg-white shadow-xl" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                @auth
                    @php($role = auth()->user()->role)
                    @if($role === 'superadmin')
                        @include('layouts.partials.sidebar-superadmin')
                    @elseif($role === 'admin')
                        @include('layouts.partials.sidebar-admin')
                    @elseif($role === 'kesbangpol')
                        @include('layouts.partials.sidebar-kesbangpol')
                    @else
                        @include('layouts.partials.sidebar-user')
                    @endif
                @endauth
            </div>
        </div>

        </div>
    </body>
    <script>
        (function() {
            const TIMEOUT_MS = 30 * 60 * 1000; // 30 menit
            let idleTimer;

            const logout = async () => {
                try {
                    await fetch("{{ route('logout') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    // abaikan error jaringan
                } finally {
                    window.location.href = "{{ route('login') }}";
                }
            };

            const resetTimer = () => {
                clearTimeout(idleTimer);
                idleTimer = setTimeout(logout, TIMEOUT_MS);
            };

            const events = ['load','mousemove','mousedown','click','scroll','keypress','touchstart','touchmove'];
            events.forEach(ev => window.addEventListener(ev, resetTimer, { passive: true }));
            resetTimer();
        })();
    </script>
</html>
