<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Peneliti')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0e9b7b3.js" crossorigin="anonymous" defer></script>
</head>
<body class="bg-gray-100 flex">

    <!-- Sidebar User -->
    <aside class="w-64 bg-blue-900 text-white min-h-screen">
        <div class="p-4 text-2xl font-bold border-b border-blue-700">
            Peneliti
        </div>

        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded hover:bg-blue-700">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>

            <a href="{{ route('researches.create') }}" class="block py-2 px-3 rounded hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i> Unggah Penelitian
            </a>

            <a href="{{ route('researches.index') }}" class="block py-2 px-3 rounded hover:bg-blue-700">
                <i class="fas fa-list mr-2"></i> Penelitian Saya
            </a>

            <a href="{{ route('profile.edit') }}" class="block py-2 px-3 rounded hover:bg-blue-700">
                <i class="fas fa-user mr-2"></i> Profil
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-3 rounded hover:bg-blue-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                </button>
            </form>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 p-6">
        <h1 class="text-2xl font-semibold mb-4">@yield('title')</h1>
        @yield('content')
    </main>

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
                } catch (e) {} finally {
                    window.location.href = "{{ route('login') }}";
                }
            };
            const resetTimer = () => { clearTimeout(idleTimer); idleTimer = setTimeout(logout, TIMEOUT_MS); };
            ['load','mousemove','mousedown','click','scroll','keypress','touchstart','touchmove']
                .forEach(ev => window.addEventListener(ev, resetTimer, { passive: true }));
            resetTimer();
        })();
    </script>
</body>
</html>
