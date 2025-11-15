<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://kit.fontawesome.com/a2e0e9b7b3.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-orange-50 via-white to-slate-50 text-slate-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">

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

                    <!-- Mobile sidebar toggle bar -->
                    <div class="md:hidden bg-white/90 border-b border-orange-100 backdrop-blur">
                        <div class="px-4 py-2 flex items-center justify-between">
                            <p class="text-xs uppercase font-semibold tracking-widest text-orange-500">Menu Utama</p>
                            <button @click="sidebarOpen = true" type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-900 text-white hover:bg-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5h14a1 1 0 100-2H3a1 1 0 000 2zm14 4H3a1 1 0 000 2h14a1 1 0 100-2zm0 6H3a1 1 0 000 2h14a1 1 0 100-2z" clip-rule="evenodd" /></svg>
                                <span>Menu</span>
                            </button>
                        </div>
                    </div>

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

            <!-- Mobile sidebar overlay -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-50 md:hidden" aria-hidden="true">
                <div @click="sidebarOpen = false" class="fixed inset-0 bg-black/50"></div>
                <div class="fixed inset-y-0 left-0 w-64 transform transition-transform duration-200" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
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
