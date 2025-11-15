@extends('layouts.public')

@section('title', config('app.name', 'Aplikasi').' | Katalog Penelitian')

@php
    $researchCollection = isset($researches) ? collect($researches) : collect();
    $totalResearchCount = isset($researches)
        ? (method_exists($researches, 'total') ? $researches->total() : $researchCollection->count())
        : 0;
    $totalInstitutionCount = $researchCollection->pluck('institution_id')->filter()->unique()->count();
    $fieldCollection = isset($fields) ? collect($fields) : collect();
    $totalFieldCount = $fieldCollection->count();
    $hasFields = $totalFieldCount > 0;
    $heroHighlights = collect([
        ['number' => 1, 'text' => 'Kurasi otomatis dari institusi resmi'],
        ['number' => 2, 'text' => 'Filter cerdas berdasarkan bidang dan tahun'],
        ['number' => 3, 'text' => 'Statistik instan untuk kebutuhan pelaporan'],
        ['number' => 4, 'text' => 'Responsif di semua perangkat'],
    ]);
    $statCards = collect([
        [
            'label' => 'Penelitian Terdaftar',
            'value' => number_format($totalResearchCount),
            'description' => 'Riset yang telah melewati proses verifikasi',
        ],
        [
            'label' => 'Institusi Aktif',
            'value' => number_format($totalInstitutionCount),
            'description' => 'Kolaborasi lintas lembaga',
        ],
        [
            'label' => 'Bidang Riset',
            'value' => number_format($totalFieldCount),
            'description' => 'Prioritas pembangunan daerah',
        ],
    ]);
    $journeySteps = collect([
        [
            'label' => 'Langkah 1',
            'title' => 'Jelajahi Katalog',
            'description' => 'Gunakan filter untuk menemukan penelitian berdasarkan tema, tahun, atau institusi.',
        ],
        [
            'label' => 'Langkah 2',
            'title' => 'Analisis Temuan',
            'description' => 'Bandingkan data antar bidang dan manfaatkan statistik publik untuk rekomendasi kebijakan.',
        ],
        [
            'label' => 'Langkah 3',
            'title' => 'Kolaborasi',
            'description' => 'Hubungi peneliti atau institusi terkait lalu lanjutkan kerja sama berbasis data.',
        ],
    ]);
@endphp

@section('hero')
    <div class="grid lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] gap-12 items-center">
        <div class="space-y-6">
            <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-orange-600">
                <span class="h-1.5 w-1.5 rounded-full bg-orange-600"></span>
                Portal Penelitian Terbuka
            </p>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight">Eksplorasi Riset Terbaru di {{ config('app.name', 'Aplikasi') }}</h1>
            <p class="text-gray-600 text-lg">Semua penelitian yang disetujui admin tersaji dalam satu etalase digital. Temukan kolaborator, pantau isu strategis daerah, dan unduh data pendukung tanpa berpindah laman.</p>
            <ul class="grid sm:grid-cols-2 gap-4 text-sm text-gray-700">
                @foreach($heroHighlights as $highlight)
                    <li class="flex items-start gap-3">
                        <span class="mt-1 h-5 w-5 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-semibold">{{ $highlight['number'] }}</span>
                        {{ $highlight['text'] }}
                    </li>
                @endforeach
            </ul>
            <div class="flex flex-wrap gap-3">
                <a href="#katalog" class="inline-flex items-center rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500">Lihat Katalog Publik</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Buka Dashboard</a>
                    @else
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Masuk</a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">Daftar</a>
                            @endif
                        </div>
                    @endauth
                @endif
            </div>
        </div>
        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 border border-orange-100 shadow-lg shadow-orange-100/50">
            <p class="text-sm font-semibold text-gray-900 mb-4">Cari Penelitian</p>
            <form method="GET" action="{{ url('/') }}" class="space-y-4">
                <div>
                    <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Judul atau Penulis</label>
                    <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Masukkan kata kunci" class="border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-lg w-full" />
                </div>
                @if($hasFields)
                    <div>
                        <label for="field" class="block text-sm font-medium text-gray-700 mb-1">Bidang Penelitian</label>
                        <select id="field" name="field" class="border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-lg w-full">
                            <option value="">Semua bidang</option>
                            @foreach($fieldCollection as $f)
                                <option value="{{ $f->id }}" @selected(request('field') == $f->id)>{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input id="year" type="number" name="year" value="{{ request('year') }}" min="2000" max="{{ date('Y') }}" class="border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-lg w-full" />
                    </div>
                    <div>
                        <label for="institution" class="block text-sm font-medium text-gray-700 mb-1">Institusi</label>
                        <input id="institution" type="text" name="institution" value="{{ request('institution') }}" placeholder="Nama institusi" class="border-gray-200 focus:border-orange-500 focus:ring-orange-500 rounded-lg w-full" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-800">Terapkan Filter</button>
                    @if(request()->hasAny(['q','field','year','institution']))
                        <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <section class="grid sm:grid-cols-3 gap-4">
        @foreach($statCards as $card)
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $card['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $card['description'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach($journeySteps as $step)
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-6">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">{{ $step['label'] }}</p>
                    <h3 class="text-lg font-semibold mt-2 text-gray-900">{{ $step['title'] }}</h3>
                    <p class="text-sm text-gray-600 mt-3">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if(isset($researches))
        @if($researches->count())
            <section id="katalog" class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Katalog Publik</p>
                        <h2 class="text-2xl font-semibold text-gray-900">Penelitian Disetujui Terbaru</h2>
                    </div>
                    <span class="text-sm text-gray-500 text-right">
                        @if(method_exists($researches, 'total'))
                            Menampilkan {{ $researches->firstItem() }}-{{ $researches->lastItem() }} dari {{ $researches->total() }} entri
                        @else
                            Menampilkan {{ $researches->count() }} entri terbaru
                        @endif
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-6 py-3 text-left">Judul</th>
                                <th class="px-6 py-3 text-left">Penulis</th>
                                <th class="px-6 py-3 text-left">Bidang</th>
                                <th class="px-6 py-3 text-left">Institusi</th>
                                <th class="px-6 py-3 text-left">Tahun</th>
                                <th class="px-6 py-3 text-left">Periode</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($researches as $r)
                                @php
                                    $startDate = $r->start_date;
                                    $endDate = $r->end_date;
                                    $startLabel = $startDate ? $startDate->format('d M Y') : '-';
                                    $endLabel = $endDate ? $endDate->format('d M Y') : '-';
                                    $statusLabel = 'Belum Dijadwalkan';
                                    $statusClasses = 'bg-gray-100 text-gray-600';
                                    $statusHint = 'Lengkapi tanggal untuk memantau progres.';

                                    if ($endDate && $endDate->isPast()) {
                                        $statusLabel = 'Selesai';
                                        $statusClasses = 'bg-emerald-100 text-emerald-700';
                                        $statusHint = 'Hubungi peneliti untuk mengunggah hasil riset.';
                                    } elseif ($startDate && $startDate->isPast()) {
                                        $statusLabel = 'Sedang Berjalan';
                                        $statusClasses = 'bg-amber-100 text-amber-700';
                                        $statusHint = 'Pantau hingga akhir periode penelitian.';
                                    } elseif ($startDate && $startDate->isFuture()) {
                                        $statusLabel = 'Terjadwal';
                                        $statusClasses = 'bg-blue-100 text-blue-700';
                                        $statusHint = 'Belum dimulai, siapkan pendampingan.';
                                    }
                                @endphp
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $r->title }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ $r->author }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ optional($r->field)->name ?: '-' }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ optional($r->institution)->name ?: '-' }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ $r->year }}</td>
                                    <td class="px-6 py-3 text-gray-700">
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
                                    <td class="px-6 py-3">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $statusLabel }}</span>
                                            <p class="text-xs text-gray-500">{{ $statusHint }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(method_exists($researches, 'links'))
                    <div class="px-6 py-4">
                        {{ $researches->links() }}
                    </div>
                @endif
            </section>
        @else
            <section id="katalog" class="rounded-2xl border border-dashed border-orange-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-orange-600 uppercase tracking-wide">Belum Ada Data</p>
                <h2 class="text-2xl font-semibold text-gray-900 mt-3">Penelitian belum tersedia</h2>
                <p class="text-gray-600 mt-2">Segera kembali lagi untuk melihat pembaruan penelitian terbaru dari berbagai institusi.</p>
            </section>
        @endif
    @endif
@endsection
