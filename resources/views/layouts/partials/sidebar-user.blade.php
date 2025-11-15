<aside class="w-64 bg-white/90 backdrop-blur text-gray-700 min-h-screen h-screen sticky top-0 overflow-y-auto border-r border-orange-100 shadow-2xl md:fixed md:inset-y-0 md:left-0 md:top-0 md:z-40 md:h-screen">
    <div class="px-5 py-6 border-b border-orange-100">
        <p class="text-[11px] font-semibold tracking-[0.3em] uppercase text-orange-500">Pengguna</p>
        <p class="text-xl font-bold text-gray-900 mt-2">Peneliti</p>
        <p class="text-xs text-gray-500 mt-1">Unggah dan pantau progres penelitian Anda</p>
    </div>
    <nav class="p-4 space-y-1 text-sm">
        @php
            $userLinks = [
                ['title' => 'Dashboard', 'icon' => 'fa-home', 'route' => 'dashboard', 'match' => 'dashboard'],
                ['title' => 'Unggah Penelitian', 'icon' => 'fa-upload', 'route' => 'researches.create', 'match' => 'researches.create'],
                ['title' => 'Penelitian Saya', 'icon' => 'fa-list', 'route' => 'researches.index', 'match' => 'researches.index'],
                ['title' => 'Unggah Hasil', 'icon' => 'fa-file-upload', 'route' => 'researches.results.my', 'match' => 'researches.results.*'],
                ['title' => 'Profil', 'icon' => 'fa-user', 'route' => 'profile.edit', 'match' => 'profile.edit'],
            ];
        @endphp
        @foreach($userLinks as $link)
            @php($isActive = request()->routeIs($link['match']))
            <a href="{{ route($link['route']) }}"
               class="flex items-center gap-3 rounded-xl border px-3 py-2.5 transition {{ $isActive ? 'bg-orange-50 border-orange-200 text-orange-900 shadow-sm' : 'border-transparent text-gray-600 hover:border-orange-100 hover:bg-white hover:text-gray-900' }}">
                <span class="h-8 w-8 rounded-lg flex items-center justify-center {{ $isActive ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas {{ $link['icon'] }}"></i>
                </span>
                <span class="font-medium">{{ $link['title'] }}</span>
            </a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}" class="pt-3">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 rounded-xl border px-3 py-2.5 text-left text-gray-600 hover:text-gray-900 hover:border-orange-100 hover:bg-white transition">
                <span class="h-8 w-8 rounded-lg flex items-center justify-center bg-gray-100 text-gray-400">
                    <i class="fas fa-sign-out-alt"></i>
                </span>
                <span class="font-medium">Keluar</span>
            </button>
        </form>
    </nav>
</aside>
