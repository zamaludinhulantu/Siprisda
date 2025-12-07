<nav class="bg-white/90 backdrop-blur border-b border-orange-100 shadow-sm relative z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-6">
        <div class="flex h-14 sm:h-16 items-center justify-between gap-3 sm:gap-5">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-1.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-7 w-auto sm:h-9 md:h-11 translate-y-[1px]" />
                        <div class="flex flex-col leading-tight min-w-0 max-w-[200px] sm:max-w-none">
                            <span class="text-[14px] sm:text-[18px] md:text-[22px] font-bold tracking-[0.02em] text-[#0f3d73] uppercase">SIPRISDA</span>
                            <span class="text-[10px] sm:text-[11px] md:text-sm font-medium text-[#2f4f74]">Sistem Informasi Penelitian dan Riset Daerah</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('researches.index')" :active="request()->routeIs('researches.*')">
                        Data Penelitian
                    </x-nav-link>
                    <x-nav-link :href="route('reports.statistics')" :active="request()->routeIs('reports.statistics')">
                        Statistik
                    </x-nav-link>
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_',' ',Auth::user()->role) }}</p>
                </div>
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                            <span>Menu</span>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.061l-4.24 4.243a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile sidebar trigger -->
            <div class="flex items-center sm:hidden shrink-0">
                <button @click="sidebarOpen = true" class="inline-flex items-center justify-center h-10 w-10 rounded-lg border border-[#0f3d73] bg-white text-[#0f3d73] hover:bg-[#e7f5ff] focus:outline-none focus:ring-2 focus:ring-[#0f3d73]/60 transition duration-150 ease-in-out">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
