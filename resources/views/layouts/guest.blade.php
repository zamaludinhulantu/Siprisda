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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-orange-50 via-white to-slate-50">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="grid gap-10 w-full max-w-5xl lg:grid-cols-[1.1fr_0.9fr] items-center">
                <div class="hidden lg:block space-y-6">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="h-12 w-12 fill-current text-orange-600" />
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Aplikasi') }}</span>
                            <span class="text-xs uppercase tracking-[0.3em] text-gray-500">Portal Penelitian</span>
                        </div>
                    </a>
                    <p class="text-lg text-gray-600">Masuk untuk mengelola data penelitian, memantau status, dan mempublikasikan hasil riset.</p>
                </div>
                <div class="w-full px-6 py-8 bg-white/90 backdrop-blur border border-orange-100 shadow-xl rounded-2xl">
                    <div class="lg:hidden mb-4 flex justify-center">
                        <a href="/">
                            <x-application-logo class="h-12 w-12 fill-current text-orange-600" />
                        </a>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
