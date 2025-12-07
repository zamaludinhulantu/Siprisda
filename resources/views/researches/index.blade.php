<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Riset Saya</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Daftar Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pantau status pengajuan dan buka detail untuk memperbarui informasi.</p>
            </div>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-plus text-xs"></i> Unggah Penelitian
            </a>
        </div>
    </x-slot>

    @php
        $isPaginator = method_exists($researches, 'firstItem');
        $startNumber = $isPaginator ? $researches->firstItem() : 1;
        $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
        $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
    @endphp

    <div class="space-y-6">
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Total Pengajuan</p>
                    <p class="text-sm text-gray-500">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} penelitian</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Status realtime
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Periode</th>
                            <th class="px-6 py-3 text-left">Kontak</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($researches as $research)
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
                                $canEdit = in_array($status, ['draft', 'submitted', 'rejected'], true);
                                $canDelete = in_array($status, ['draft', 'submitted'], true);
                                $startDate = optional($research->start_date ?? $research->submitted_at ?? $research->created_at)->format('d M Y');
                                $endDate = optional($research->end_date ?? $research->submitted_at ?? $research->created_at)->format('d M Y');
                                $yearLabel = $research->year
                                    ?: optional($research->start_date)->format('Y')
                                    ?: optional($research->end_date)->format('Y')
                                    ?: optional($research->submitted_at ?? $research->created_at)->format('Y')
                                    ?: 'Belum diisi';
                                $periodLabel = $startDate && $endDate
                                    ? $startDate . ' s/d ' . $endDate
                                    : ($startDate ?: ($endDate ?: 'Belum diisi'));
                            @endphp
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Peneliti: {{ $research->author ?? '-' }}</p>
                                    <p class="text-[11px] text-gray-400 mt-1">ID: {{ $research->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <p class="font-semibold text-gray-900">{{ optional($research->institution)->name ?? 'Institusi belum diisi' }}</p>
                                    @if($research->keywords)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $research->keywords }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div class="space-y-1 text-xs">
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 font-semibold text-orange-700 ring-1 ring-orange-100">Periode: {{ $periodLabel }}</span>
                                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-100">Tahun {{ $yearLabel }}</span>
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
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('researches.show', $research->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                            <i class="fas fa-eye text-[11px]"></i> Detail
                                        </a>
                                        @if($canEdit)
                                            <a href="{{ route('researches.edit', $research->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                                <i class="fas fa-pen text-[11px]"></i> Edit
                                            </a>
                                        @endif
                                        @if($canDelete)
                                            <form action="{{ route('researches.destroy', $research->id) }}" method="POST" onsubmit="return confirm('Hapus penelitian ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    <i class="fas fa-trash text-[11px]"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-gray-500 text-sm">Belum ada penelitian. Unggah penelitian pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $researches->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
