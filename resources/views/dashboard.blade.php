@php
    $currentRole = Auth::user()->role;
    $isAdminPanel = in_array($currentRole, ['admin', 'superadmin']);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Ringkasan</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Dashboard') }}</h2>
                <p class="text-sm text-gray-500">Pantau progres pengajuan dan status persetujuan penelitian.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 capitalize">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>{{ str_replace('_',' ',Auth::user()->role) }}
                </span>
                @if($isAdminPanel)
                    <a href="{{ Auth::user()->isSuperAdmin() ? route('admin.researches.index') : route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-database text-xs"></i>
                        Kelola Penelitian
                    </a>
                @else
                    <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-white">
                        <i class="fas fa-upload text-xs"></i>
                        Ajukan Penelitian
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="bg-white/90 backdrop-blur border border-orange-100 shadow-sm rounded-2xl p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Halo, {{ Auth::user()->name }}</p>
                    <h3 class="text-xl font-semibold text-gray-900 mt-1">Semua kanal sudah sinkron.</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($isAdminPanel)
                            Gunakan angka berikut untuk memantau antrian verifikasi sebelum publikasi.
                        @else
                            Ringkasan status penelitian Anda akan diperbarui otomatis setiap ada feedback dari admin.
                        @endif
                    </p>
                </div>
                <div class="flex flex-col text-sm text-gray-500">
                    <span>Tanggal: <strong>{{ now()->translatedFormat('d F Y') }}</strong></span>
                    <span>Zona waktu: <strong>WIB</strong></span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-orange-100 bg-orange-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-10 w-10 rounded-xl bg-white text-orange-600 flex items-center justify-center">
                            <i class="fas fa-database"></i>
                        </span>
                        <div>
                            <p class="text-xs uppercase font-semibold tracking-wide text-orange-600">Total Penelitian</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $total }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-10 w-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div>
                            <p class="text-xs uppercase font-semibold tracking-wide text-emerald-600">Disetujui</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $approved }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-10 w-10 rounded-xl bg-white text-rose-600 flex items-center justify-center">
                            <i class="fas fa-times-circle"></i>
                        </span>
                        <div>
                            <p class="text-xs uppercase font-semibold tracking-wide text-rose-600">Ditolak</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $rejected }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                    <div class="flex items-center gap-3">
                        <span class="h-10 w-10 rounded-xl bg-white text-amber-600 flex items-center justify-center">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        <div>
                            <p class="text-xs uppercase font-semibold tracking-wide text-amber-600">Diajukan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $submitted }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($isAdminPanel)
            <section class="rounded-3xl border border-[#cde3ff] bg-white p-6 shadow-md shadow-[#cde3ff]/50">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#0f3d73]">Cari Penelitian</p>
                        <h3 class="text-lg font-semibold text-gray-900">Filter cepat di dashboard</h3>
                        <p class="text-sm text-gray-600">Judul/Penulis • Bidang • Tahun • Institusi</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-database text-xs"></i> Masuk Daftar
                        </a>
                        @if(config('spk.auto_rank_enabled'))
                            <a href="{{ route('spk.auto-rank') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#b7d4ff] bg-[#e7f5ff] px-4 py-2 text-sm font-semibold text-[#0f3d73] hover:bg-[#d5e9ff]">
                                <i class="fas fa-sitemap text-xs"></i> SPK (SAW)
                            </a>
                        @endif
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.researches.index') }}" class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="md:col-span-2">
                        <label for="q" class="text-sm font-medium text-gray-700">Judul atau Penulis</label>
                        <input type="text" id="q" name="q" placeholder="Masukkan kata kunci" class="mt-1 w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 bg-white" value="{{ request('q') }}">
                    </div>
                    <div>
                        <label for="field_id" class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                        <select id="field_id" name="field_id" class="mt-1 w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Semua bidang</option>
                            @foreach($fields ?? [] as $field)
                                <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>{{ $field->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year" class="text-sm font-medium text-gray-700">Tahun</label>
                        <select id="year" name="year" class="mt-1 w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 bg-white">
                            <option value="">Semua tahun</option>
                            @foreach(($years ?? []) as $year)
                                <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="institution" class="text-sm font-medium text-gray-700">Institusi</label>
                        <input type="text" id="institution" name="institution" placeholder="Nama institusi" class="mt-1 w-full rounded-xl border-gray-200 focus:border-orange-500 focus:ring-orange-500 bg-white" value="{{ request('institution') }}">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                            <i class="fas fa-search text-xs mr-2"></i> Terapkan Filter
                        </button>
                        @if(request()->hasAny(['q','field_id','year','institution','status']))
                            <a href="{{ route('admin.researches.index') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">Reset</a>
                        @endif
                        @if(config('spk.auto_rank_enabled'))
                            <a href="{{ route('spk.auto-rank') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#b7d4ff] bg-[#e7f5ff] px-4 py-3 text-sm font-semibold text-[#0f3d73] hover:bg-[#d5e9ff]">
                                <i class="fas fa-sitemap text-xs"></i> Buka SPK
                            </a>
                        @endif
                    </div>
                </form>
            </section>
        @endif

        @if(isset($recentResearches) && $recentResearches->count())
            <section class="rounded-2xl border border-gray-100 bg-white shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Monitoring</p>
                        <h3 class="text-xl font-semibold text-gray-900">Periode Penelitian Terbaru</h3>
                        <p class="text-sm text-gray-500">Awasi jadwal mulai & selesai agar tindak lanjut unggah hasil tepat waktu.</p>
                    </div>
                    <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-500">
                        Lihat Semua
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
                <div class="overflow-x-auto mt-6">
                    <table class="min-w-full table-auto divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-2 text-left">Judul</th>
                                <th class="px-4 py-2 text-left">Peneliti</th>
                                <th class="px-4 py-2 text-left">Periode</th>
                                <th class="px-4 py-2 text-left">Status Lapangan</th>
                                <th class="px-4 py-2 text-left">Status Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($recentResearches as $research)
                                @php
                                    $startDate = $research->start_date;
                                    $endDate = $research->end_date;
                                    $startLabel = $startDate ? $startDate->format('d M Y') : '-';
                                    $endLabel = $endDate ? $endDate->format('d M Y') : '-';
                                    $fieldLabel = optional($research->field)->name ?: 'Umum';

                                    $statusLabel = 'Belum Dijadwalkan';
                                    $statusClasses = 'bg-gray-100 text-gray-600';
                                    $statusHint = 'Isi tanggal mulai/selesai untuk memantau progres.';

                                    if ($endDate && $endDate->isPast()) {
                                        $statusLabel = 'Selesai';
                                        $statusClasses = 'bg-emerald-100 text-emerald-700';
                                        $statusHint = 'Hubungi peneliti untuk unggah hasil penelitian.';
                                    } elseif ($startDate && $startDate->isPast()) {
                                        $statusLabel = 'Sedang Berjalan';
                                        $statusClasses = 'bg-amber-100 text-amber-700';
                                        $statusHint = 'Pastikan monitoring akhir sesuai jadwal.';
                                    } elseif ($startDate && $startDate->isFuture()) {
                                        $statusLabel = 'Terjadwal';
                                        $statusClasses = 'bg-blue-100 text-blue-700';
                                        $statusHint = 'Siapkan pendampingan sebelum mulai.';
                                    }

                                    $approvalLabel = ucfirst($research->status ?? 'draft');
                                    $approvalClasses = match ($research->status) {
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-100',
                                        'submitted' => 'bg-amber-50 text-amber-700 ring-amber-100',
                                        default => 'bg-gray-50 text-gray-600 ring-gray-100',
                                    };
                                @endphp
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-4 py-3 text-gray-900 font-medium">
                                        <p>{{ $research->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $fieldLabel }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $research->author ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        <div class="space-y-1 text-xs">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-gray-500 font-medium">Mulai</span>
                                                <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 font-semibold text-orange-700">{{ $startLabel }}</span>
                                            </div>
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-gray-500 font-medium">Selesai</span>
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">{{ $endLabel }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                                            <p class="text-xs text-gray-500">{{ $statusHint }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $approvalClasses }}">{{ $approvalLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-gray-900">Agenda Singkat</h4>
                <ul class="mt-4 space-y-4 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-orange-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">Tinjau pengajuan baru</p>
                            <p>@if($isAdminPanel) Prioritaskan verifikasi agar publik dapat melihat hasil terbaru.@else Pastikan dokumen unggahan Anda lengkap sebelum batas review.@endif</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">Perbarui metadata</p>
                            <p>Bidang dan institusi harus konsisten untuk memudahkan pencarian publik.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-slate-400"></span>
                        <div>
                            <p class="font-medium text-gray-900">Bagikan tautan publik</p>
                            <p>Arahkan mitra ke laman statistik untuk insight cepat.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white/70 backdrop-blur p-6 shadow-sm">
                <h4 class="text-lg font-semibold text-gray-900">Tautan Cepat</h4>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($isAdminPanel && config('spk.auto_rank_enabled'))
                        <a href="{{ route('spk.auto-rank') }}" class="rounded-xl border border-orange-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50">
                            SPK Otomatis (SAW)
                        </a>
                    @endif
                    <a href="{{ route('public.statistics') }}" class="rounded-xl border border-orange-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50">
                        Statistik Publik
                    </a>
                    <a href="{{ route('public.guide') }}" class="rounded-xl border border-orange-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50">
                        Panduan Integrasi
                    </a>
                    <a href="{{ route('researches.index') }}" class="rounded-xl border border-orange-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50">
                        Daftar Penelitian
                    </a>
                    <a href="{{ route('profile.edit') }}" class="rounded-xl border border-orange-100 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-orange-50">
                        Pengaturan Profil
                    </a>
                </div>
                @if($isAdminPanel)
                    <p class="mt-4 text-xs text-gray-500">Butuh laporan khusus? Unduh statistik lengkap melalui menu laporan.</p>
                @else
                    <p class="mt-4 text-xs text-gray-500">Perlu bantuan? Cek panduan atau hubungi admin melalui kontak resmi.</p>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>






