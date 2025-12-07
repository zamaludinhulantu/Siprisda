<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portal Riset'))</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logosiprisda.png') }}">
    @include('layouts.partials.brand-theme')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gradient-to-br from-[#e7f5ff] via-white to-[#eaf9f3] text-gray-900 antialiased">
    @php
        $navLinks = collect([
            ['label' => 'Beranda', 'route' => '/', 'active' => request()->is('/')],
            ['label' => 'Statistik', 'route' => route('public.statistics'), 'active' => request()->routeIs('public.statistics')],
            ['label' => 'Panduan', 'route' => route('public.guide'), 'active' => request()->routeIs('public.guide')],
        ]);
        $submenu = collect();
        if (Route::has('about')) {
            $submenu->push(['label' => 'Tentang Kami', 'route' => route('about'), 'active' => request()->routeIs('about')]);
        }
        if (Route::has('public.institutions')) {
            $submenu->push(['label' => 'Institusi', 'route' => route('public.institutions'), 'active' => request()->routeIs('public.institutions')]);
        }
        if (Route::has('news.index')) {
            $submenu->push(['label' => 'Berita', 'route' => route('news.index'), 'active' => request()->routeIs('news.*')]);
        }
        if ($submenu->isNotEmpty()) {
            $navLinks->push([
                'label' => 'Tentang',
                'route' => null,
                'active' => $submenu->contains('active', true),
                'submenu' => $submenu
            ]);
        }
    @endphp
    <div class="min-h-screen flex flex-col">
        <header class="bg-white/90 backdrop-blur border-b border-[#cde3ff] shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-2 md:gap-3 py-2">
                <a href="/" class="flex items-center gap-2 min-w-0 flex-1">
                    <x-application-logo class="h-8 w-auto sm:h-9 md:h-10 translate-y-[1px] shrink-0" />
                    <div class="flex flex-col leading-snug min-w-0">
                        <span class="text-[12px] sm:text-[15px] md:text-[18px] font-bold tracking-[0.02em] text-[#0f3d73] uppercase truncate">SIPRISDA</span>
                        <span class="text-[9px] sm:text-[11px] md:text-xs font-medium text-[#2f4f74] leading-tight">Sistem Informasi Penelitian dan Riset Daerah</span>
                    </div>
                </a>
                <nav class="hidden md:flex items-center gap-2 text-sm text-gray-700">
                    @foreach($navLinks as $link)
                        @if(isset($link['submenu']))
                            @php $firstSubRoute = $link['submenu']->first()['route'] ?? '#'; @endphp
                            <div class="relative group">
                                <a href="{{ $firstSubRoute }}" class="inline-flex items-center rounded-md px-2 py-1 {{ $link['active'] ? 'bg-[#e7f5ff] text-[#0f3d73] font-semibold' : 'hover:text-gray-900 hover:bg-[#e7f5ff]' }}">
                                    {{ $link['label'] }}
                                </a>
                                <div class="absolute top-full left-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    @foreach($link['submenu'] as $sub)
                                        <a href="{{ $sub['route'] }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#e7f5ff] hover:text-[#0f3d73] {{ $sub['active'] ? 'bg-[#e7f5ff] text-[#0f3d73] font-semibold' : '' }}">
                                            {{ $sub['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $link['route'] }}" class="inline-flex items-center rounded-md px-2 py-1 {{ $link['active'] ? 'bg-[#e7f5ff] text-[#0f3d73] font-semibold' : 'hover:text-gray-900 hover:bg-[#e7f5ff]' }}">
                                {{ $link['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
                <div class="hidden md:flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-2.5 py-1 text-sm font-semibold text-gray-900 hover:bg-white">
                            <i class="fas fa-gauge text-xs text-[#0f3d73]"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-md bg-gray-900 px-2.5 py-1 text-sm font-semibold text-white hover:bg-gray-800">
                                <i class="fas fa-sign-out-alt text-xs"></i> Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-2.5 py-1 text-sm font-semibold text-gray-900 hover:bg-white">
                            <i class="fas fa-lock text-xs text-[#0f3d73]"></i> Masuk
                        </a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 rounded-md bg-gray-900 px-2.5 py-1 text-sm font-semibold text-white hover:bg-gray-800">
                                <i class="fas fa-user-plus text-xs"></i> Daftar
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
            <!-- Mobile nav -->
            <div class="md:hidden max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-2">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar text-sm text-gray-700">
                    @foreach($navLinks as $link)
                        @php
                            $target = $link['route'] ?? ($link['submenu']->first()['route'] ?? '#');
                            $isActive = $link['active'] ?? false;
                        @endphp
                        <a href="{{ $target }}" class="inline-flex whitespace-nowrap items-center rounded-full px-3 py-1.5 border {{ $isActive ? 'bg-[#e7f5ff] border-[#cde3ff] text-[#0f3d73] font-semibold' : 'border-gray-200 text-gray-700 hover:border-[#cde3ff]' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </header>

        @hasSection('hero')
            <section class="bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3]">
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

        <footer class="bg-white border-t border-[#cde3ff]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-sm text-gray-500">
                <div>
                    <p class="font-semibold text-gray-900">&copy; {{ date('Y') }} SIPRISDA</p>
                    <p class="text-xs">Portal publik resmi SIPRISDA untuk keterbukaan data riset Bappeda.</p>
                </div>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
