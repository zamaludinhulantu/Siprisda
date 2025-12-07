<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Super Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Tambah Pengguna Baru</h1>
                <p class="text-sm text-gray-500">Buat akun dan tetapkan peran tanpa menunggu proses registrasi mandiri.</p>
            </div>
            <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm space-y-4">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Peran</label>
                    <select name="role" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        @foreach($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Institusi</label>
                    <input list="institution-list" type="text" name="institution_name" value="{{ old('institution_name') }}" placeholder="opsional" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                    <datalist id="institution-list">
                        @foreach($institutions as $institution)
                            <option value="{{ $institution->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Password (opsional)</label>
                <input type="text" name="password" value="{{ old('password') }}" placeholder="Kosongkan untuk membuat password acak" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
            </div>
            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                    <i class="fas fa-user-plus text-xs"></i> Simpan Pengguna
                </button>
                <p class="text-xs text-gray-500">Password acak akan ditampilkan setelah penyimpanan untuk dibagikan ke pengguna.</p>
            </div>
        </form>

        <div class="rounded-2xl border border-gray-100 bg-white/80 backdrop-blur p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Panduan Cepat</h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-orange-500"></span>
                    <div>
                        <p class="font-medium text-gray-900">Peran super admin</p>
                        <p>Gunakan hanya untuk tim inti. Peran ini tidak dibatasi fitur apapun.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                    <div>
                        <p class="font-medium text-gray-900">Institusi opsional</p>
                        <p>Isi nama institusi baru jika belum ada di daftar, sistem akan membuatkannya.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full bg-slate-400"></span>
                    <div>
                        <p class="font-medium text-gray-900">Password sementara</p>
                        <p>Biarkan kosong untuk password acak; minta pengguna mengganti di menu profil.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>
