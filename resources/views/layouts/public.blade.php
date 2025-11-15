<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Portal Riset'))</title>
    @stack('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gradient-to-br from-orange-50 via-white to-slate-50 text-gray-900 antialiased">
    @php
        $navLinks = collect([
            ['label' => 'Beranda', 'route' => '/', 'active' => request()->is('/')],
            ['label' => 'Institusi', 'route' => route('public.institutions'), 'active' => request()->routeIs('public.institutions')],
            ['label' => 'Statistik', 'route' => route('public.statistics'), 'active' => request()->routeIs('public.statistics')],
            ['label' => 'Panduan', 'route' => route('public.guide'), 'active' => request()->routeIs('public.guide')],
        ]);
        if (Route::has('news.index')) {
            $navLinks->push(['label' => 'Berita', 'route' => route('news.index'), 'active' => request()->routeIs('news.*')]);
        }
        if (Route::has('about')) {
            $navLinks->push(['label' => 'Tentang', 'route' => route('about'), 'active' => request()->routeIs('about')]);
        }
        if (Route::has('contact')) {
            $navLinks->push(['label' => 'Kontak', 'route' => route('contact'), 'active' => request()->routeIs('contact')]);
        }
    @endphp
    <div class="min-h-screen flex flex-col">
        <header class="bg-white/90 backdrop-blur border-b border-orange-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <x-application-logo class="h-8 w-8 fill-current text-orange-600" />
                    <div class="flex flex-col leading-tight">
                        <span class="font-semibold text-lg text-gray-900">{{ config('app.name', 'Aplikasi') }}</span>
                        <span class="text-[11px] uppercase tracking-[0.3em] text-gray-400">Portal Publik</span>
                    </div>
                </a>
                <nav class="hidden md:flex items-center gap-4 text-sm text-gray-700">
                    @foreach($navLinks as $link)
                        <a href="{{ $link['route'] }}" class="inline-flex items-center rounded-full px-3 py-1.5 {{ $link['active'] ? 'bg-orange-100 text-orange-700 font-semibold' : 'hover:text-gray-900 hover:bg-orange-50' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-900 hover:bg-white">
                        <i class="fas fa-lock text-xs text-orange-500"></i> Masuk
                    </a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-semibold text-white hover:bg-gray-800">
                            <i class="fas fa-user-plus text-xs"></i> Daftar
                        </a>
                    @endif
                </div>
            </div>
        </header>

        @hasSection('hero')
            <section class="bg-gradient-to-r from-orange-50 via-white to-orange-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    @yield('hero')
                </div>
            </section>
        @endif

        <main class="flex-1 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t border-orange-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-sm text-gray-500">
                <div>
                    <p class="font-semibold text-gray-900">&copy; {{ date('Y') }} {{ config('app.name', 'Aplikasi') }}</p>
                    <p class="text-xs">Portal publik resmi Bappeda untuk keterbukaan data riset.</p>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
