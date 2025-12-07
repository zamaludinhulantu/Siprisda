<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Super Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Kelola Pengguna & Role</h1>
                <p class="text-sm text-gray-500">Lihat seluruh akun dan atur akses sesuai kebutuhan tugas.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white">
                    <i class="fas fa-crown text-xs"></i> Mode Super Admin
                </span>
                <a href="{{ route('superadmin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-700 hover:bg-orange-100">
                    <i class="fas fa-user-plus text-xs"></i> Tambah Pengguna
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <form action="{{ route('superadmin.users.index') }}" method="GET" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="q" class="text-sm font-medium text-gray-700">Cari nama atau email</label>
                    <input id="q" name="q" type="text" value="{{ $search }}"
                           placeholder="contoh: dina@bappeda.go.id"
                           class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div>
                    <label for="role" class="text-sm font-medium text-gray-700">Filter peran</label>
                    <select id="role" name="role" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua peran</option>
                        @foreach($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected($roleFilter === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="institution_id" class="text-sm font-medium text-gray-700">Filter institusi</label>
                    <select id="institution_id" name="institution_id" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua institusi</option>
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}" @selected((string) $institutionFilter === (string) $institution->id)>{{ $institution->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-search text-xs mr-2"></i> Terapkan
                    </button>
                    @if ($search !== '' || $roleFilter || $institutionFilter)
                        <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-white">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </section>

        @if(isset($roleSummary))
            <section class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Ringkasan Peran</p>
                        <p class="text-sm text-gray-500">Pastikan proporsi akses sesuai struktur tim.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span> Data realtime
                    </span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($roleOptions as $value => $label)
                        @php
                            $count = $roleSummary[$value] ?? 0;
                            $color = match($value) {
                                'superadmin' => 'bg-orange-50 text-orange-700 border-orange-100',
                                'admin' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                'kesbangpol' => 'bg-blue-50 text-blue-700 border-blue-100',
                                default => 'bg-gray-50 text-gray-700 border-gray-100',
                            };
                        @endphp
                        <div class="rounded-xl border {{ $color }} px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide">{{ $label }}</p>
                            <p class="text-2xl font-bold mt-1">{{ $count }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Daftar Pengguna</p>
                    <p class="text-sm text-gray-500">
                        Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Hak akses realtime
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Pengguna</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Role Saat Ini</th>
                            <th class="px-6 py-3 text-left">Perbarui Role</th>
                            <th class="px-6 py-3 text-left">Kredensial</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ optional($user->institution)->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 capitalize">
                                        {{ str_replace('_',' ',$user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('superadmin.users.role.update', $user) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-sm" @disabled($user->id === auth()->id())>
                                            @foreach ($roleOptions as $value => $label)
                                                <option value="{{ $value }}" @selected($user->role === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800 disabled:opacity-40" @disabled($user->id === auth()->id())>
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="space-y-2">
                                        <div class="text-xs text-gray-500">Diperbarui {{ optional($user->updated_at)->diffForHumans() ?? '-' }}</div>
                                        <form method="POST" action="{{ route('superadmin.users.password.reset', $user) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                                            @csrf
                                            <input type="text" name="password" placeholder="kosongkan untuk acak" class="w-full sm:w-40 rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-xs">
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-50">
                                                <i class="fas fa-key text-[11px] mr-1.5"></i> Reset Password
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">Tidak ada pengguna yang cocok dengan kata kunci.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $users->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
