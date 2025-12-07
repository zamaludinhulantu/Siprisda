<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('img/logosiprisda.png') }}">
        @include('layouts.partials.brand-theme')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-[#e7f5ff] via-white to-[#eaf9f3]">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="grid gap-10 w-full max-w-5xl lg:grid-cols-[1.1fr_0.9fr] items-center">
                <div class="hidden lg:block space-y-6">
                    <a href="/" class="inline-flex items-center gap-1.5">
                        <x-application-logo class="h-18 w-auto translate-y-[2px]" />
                        <div class="flex flex-col leading-tight">
                            <span class="text-[28px] font-bold tracking-[0.02em] text-[#0f3d73] uppercase">SIPRISDA</span>
                            <span class="text-sm font-medium text-[#2f4f74]">Sistem Informasi Penelitian dan Riset Daerah</span>
                        </div>
                    </a>
                    <p class="text-lg text-gray-600">Masuk untuk mengelola pengajuan, verifikasi Kesbangpol, dan publikasi hasil riset.</p>
                </div>
                <div class="w-full px-6 py-8 bg-white/90 backdrop-blur border border-orange-100 shadow-xl rounded-2xl">
                    <div class="lg:hidden mb-4 flex justify-center">
                        <a href="/" class="flex flex-col items-center gap-1.5">
                            <x-application-logo class="h-14 w-auto translate-y-[1px]" />
                            <div class="text-center leading-tight">
                                <div class="text-2xl font-bold tracking-[0.02em] text-[#0f3d73] uppercase">SIPRISDA</div>
                                <div class="text-xs font-medium text-[#2f4f74]">Sistem Informasi Penelitian dan Riset Daerah</div>
                            </div>
                        </a>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
