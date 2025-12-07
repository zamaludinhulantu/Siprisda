<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Bappeda Riset</p>
                <h1 class="text-2xl font-semibold text-gray-900">Katalog Penelitian Disetujui</h1>
                <p class="text-sm text-gray-500">Hanya menampilkan penelitian yang sudah diverifikasi Kesbangpol dan siap diputuskan/ditayangkan admin.</p>
            </div>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-upload text-xs"></i> Unggah Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto px-4 lg:px-0">
        <section class="rounded-2xl border border-slate-100 bg-gradient-to-br from-slate-50 via-white to-slate-50 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Katalog</p>
                    <h2 class="text-xl font-semibold text-gray-900">Lihat Katalog</h2>
                    <p class="text-sm text-gray-600">Judul atau Penulis, Bidang Penelitian, Tahun, Institusi</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-database text-xs"></i> Masuk Daftar
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.researches.index') }}" class="mt-6 grid gap-3 grid-cols-1 md:grid-cols-2 lg:grid-cols-12 items-end">
                <div class="lg:col-span-4 md:col-span-2">
                    <label for="q" class="text-sm font-medium text-gray-700">Judul atau Penulis</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Masukkan kata kunci" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="lg:col-span-3 md:col-span-2">
                    <label for="field_id" class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                    <select id="field_id" name="field_id" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua bidang</option>
                        @foreach($fields ?? [] as $field)
                            <option value="{{ $field->id }}" @selected(request('field_id') == $field->id)>{{ $field->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2 md:col-span-1">
                    <label for="year" class="text-sm font-medium text-gray-700">Tahun</label>
                    <select id="year" name="year" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua tahun</option>
                        @foreach(($years ?? []) as $year)
                            <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2 md:col-span-1">
                    <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua status</option>
                        @foreach([
                            'draft' => 'Draft',
                            'submitted' => 'Diajukan',
                            'kesbang_verified' => 'Disetujui Kesbang',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak'
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-6 md:col-span-2">
                    <label for="institution" class="text-sm font-medium text-gray-700">Institusi</label>
                    <input type="text" id="institution" name="institution" value="{{ request('institution') }}" placeholder="Nama institusi" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="lg:col-span-6 md:col-span-2 flex flex-wrap justify-start md:justify-end items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-800 shadow-sm w-full md:w-auto">Terapkan Filter</button>
                    @if(request()->hasAny(['q','status','field_id','year','institution']))
                        <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 w-full md:w-auto">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        @php
            $isPaginator = method_exists($researches, 'firstItem');
            $startNumber = $isPaginator ? $researches->firstItem() : 1;
            $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
            $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
        @endphp
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur p-0 shadow">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daftar Penelitian</p>
                    <p class="text-sm text-gray-500">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} entri</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Sinkron real-time
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Peneliti</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Periode</th>
                            <th class="px-6 py-3 text-left">Kontak</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($researches as $research)
                            @php
                                $status = (string)($research->status ?? 'draft');
                                $statusMap = [
                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
                                    'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
                                    'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
                                    'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
                                ];
                                $statusInfo = $statusMap[$status] ?? $statusMap['default'];
                            @endphp
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->title ?? $research->judul ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">ID #{{ $research->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $research->author ?? optional($research->user)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <p class="font-semibold text-gray-900">{{ optional($research->institution)->name ?? 'Institusi belum diisi' }}</p>
                                    @if($research->keywords)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $research->keywords }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    @php
                                        $startDate = optional($research->start_date)->format('d M Y');
                                        $endDate = optional($research->end_date)->format('d M Y');
                                        $periodLabel = $startDate && $endDate
                                            ? $startDate . ' s/d ' . $endDate
                                            : ($startDate ?? ($endDate ?? '-'));
                                    @endphp
                                    <div class="space-y-1 text-xs">
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-1 text-[11px] font-semibold text-orange-700 ring-1 ring-orange-100">Periode: {{ $periodLabel }}</span>
                                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-100">Tahun {{ $research->year ?? '-' }}</span>
                                        @if($research->start_date && $research->end_date && now()->between($research->start_date, $research->end_date))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Berjalan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div class="space-y-1 text-xs text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-id-card text-[10px] text-gray-400"></i>
                                            <span>{{ $research->researcher_nik ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-phone text-[10px] text-gray-400"></i>
                                            <span>{{ $research->researcher_phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                    @if($research->resubmitted_after_reject_at)
                                        <span class="mt-1 inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-100">
                                            <i class="fas fa-rotate text-[10px] mr-1"></i> Perbaikan peneliti
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end gap-2">
                                        <a href="{{ route('admin.researches.show', $research) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye text-[11px]"></i> Detail
                                        </a>
                                        <div class="flex flex-wrap justify-end gap-1.5">
                                            @if($research->pdf_path)
                                                <a href="{{ route('admin.researches.download', [$research, 'pdf_path']) }}" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-file-alt text-[10px]"></i> Proposal
                                                </a>
                                            @endif
                                            @if($research->kesbang_letter_path)
                                                <a href="{{ route('admin.researches.download', [$research, 'kesbang_letter_path']) }}" class="inline-flex items-center gap-1 rounded-full border border-cyan-200 px-3 py-1 text-[11px] font-semibold text-cyan-700 hover:bg-cyan-50">
                                                    <i class="fas fa-file-signature text-[10px]"></i> Surat Rekom
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada data yang memenuhi filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $researches->links() }}
        </div>
    </div>
</x-app-layout>
