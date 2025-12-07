@php
    $resultCollection = collect($results);
    $hasData = $resultCollection->count() > 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">SPK Otomatis</p>
                <h2 class="text-2xl font-semibold text-gray-900">Rangking Penelitian (SAW)</h2>
                <p class="text-sm text-gray-500">Menggunakan data yang sudah ada: tahun, status persetujuan, PDF, surat Kesbang, dan jadwal.</p>
            </div>
            <div class="rounded-xl bg-orange-50 border border-orange-100 px-4 py-3 text-xs text-orange-700">
                <p class="font-semibold">Toggle cepat</p>
                <p>Set <span class="font-mono">SPK_AUTO_RANK_ENABLED=false</span> di <span class="font-mono">.env</span> untuk mematikan fitur ini.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Bobot Kriteria</p>
                        <h3 class="text-lg font-semibold text-gray-900">Kontribusi skor</h3>
                    </div>
                    <span class="text-xs text-gray-500">Total 100%</span>
                </div>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li class="flex items-center justify-between rounded-lg bg-orange-50 px-3 py-2">
                        <span>Relevansi RPJMD</span>
                        <span class="font-semibold">{{ number_format($weights['rpjmd_relevance'] * 100, 0) }}%</span>
                    </li>
                    <li class="flex items-center justify-between rounded-lg bg-rose-50 px-3 py-2">
                        <span>Urgensi</span>
                        <span class="font-semibold">{{ number_format($weights['urgency'] * 100, 0) }}%</span>
                    </li>
                    <li class="flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2">
                        <span>Kelengkapan dokumen</span>
                        <span class="font-semibold">{{ number_format($weights['completeness'] * 100, 0) }}%</span>
                    </li>
                    <li class="flex items-center justify-between rounded-lg bg-blue-50 px-3 py-2">
                        <span>Dampak</span>
                        <span class="font-semibold">{{ number_format($weights['impact'] * 100, 0) }}%</span>
                    </li>
                    <li class="flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2">
                        <span>Kesesuaian prosedur</span>
                        <span class="font-semibold">{{ number_format($weights['procedure'] * 100, 0) }}%</span>
                    </li>
                </ul>
                <p class="mt-3 text-xs text-gray-500">Semua otomatis: cek kata kunci RPJMD, kata kunci urgensi, kelengkapan dokumen wajib, indikasi dampak dari bidang/topik, dan kepatuhan format/jadwal.</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Skor Status</p>
                <h3 class="text-lg font-semibold text-gray-900">Urutan preferensi</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    @foreach($statusScores as $status => $score)
                        <li class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                            <span class="capitalize">{{ str_replace('_', ' ', $status) }}</span>
                            <span class="font-semibold">{{ $score }}</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-gray-500">Status approval menambah bonus skor tipis (5%) untuk memprioritaskan yang sudah diverifikasi.</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Dokumen Wajib</p>
                <h3 class="text-lg font-semibold text-gray-900">Komponen kelengkapan</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    @foreach($documents as $name => $weight)
                        <li class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                            <span class="capitalize">{{ str_replace('_', ' ', $name) }}</span>
                            <span class="font-semibold">{{ number_format($weight * 100, 0) }}%</span>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-gray-500">PDF = proposal, surat permohonan = kesbang_letter, KTP & surat rekomendasi diasumsikan belum tersedia sehingga dinilai 0 jika belum ada kolomnya.</p>
            </div>
        </div>

        @if(!$hasData)
            <div class="rounded-2xl border border-dashed border-orange-200 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-orange-600 uppercase tracking-wide">Belum Ada Data</p>
                <h3 class="text-xl font-semibold text-gray-900 mt-2">Tambahkan penelitian terlebih dahulu</h3>
                <p class="text-sm text-gray-500 mt-1">Rangking akan muncul otomatis ketika ada minimal satu penelitian.</p>
            </div>
        @else
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hasil Rangking</p>
                        <h3 class="text-xl font-semibold text-gray-900">SPK SAW otomatis</h3>
                        <p class="text-sm text-gray-500">Recency + kelengkapan dokumen + status persetujuan.</p>
                    </div>
                    <span class="text-xs text-gray-500">Dihitung: {{ now()->format('d M Y H:i') }} WIB</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-2 text-left">No</th>
                                <th class="px-4 py-2 text-left">Judul & Info</th>
                                <th class="px-4 py-2 text-left">Kriteria</th>
                                <th class="px-4 py-2 text-left">Skor Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($resultCollection as $index => $row)
                                @php
                                    $research = $row['research'];
                                    $fieldLabel = optional($research->field)->name ?: 'Umum';
                                    $institutionLabel = optional($research->institution)->name ?: '-';
                                    $statusLabel = ucwords(str_replace('_', ' ', $research->status ?? 'draft'));
                                @endphp
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-4 py-3 text-gray-900 font-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-900">{{ $research->title }}</p>
                                        <p class="text-xs text-gray-600">{{ $research->author ?: 'Tanpa penulis' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $fieldLabel }} · {{ $institutionLabel }} · {{ $research->year ?: 'Tahun ?' }} · Status: {{ $statusLabel }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="grid grid-cols-1 gap-1 text-xs text-gray-700">
                                            <div class="flex items-center justify-between rounded bg-orange-50 px-2 py-1">
                                                <span>RPJMD</span>
                                                <span class="font-semibold">{{ $row['scores']['rpjmd_relevance'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-rose-50 px-2 py-1">
                                                <span>Urgensi</span>
                                                <span class="font-semibold">{{ $row['scores']['urgency'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-emerald-50 px-2 py-1">
                                                <span>Kelengkapan</span>
                                                <span class="font-semibold">{{ $row['scores']['completeness'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-blue-50 px-2 py-1">
                                                <span>Dampak</span>
                                                <span class="font-semibold">{{ $row['scores']['impact'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-amber-50 px-2 py-1">
                                                <span>Prosedur</span>
                                                <span class="font-semibold">{{ $row['scores']['procedure'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-slate-100 px-2 py-1">
                                                <span>Recency</span>
                                                <span class="font-semibold">{{ $row['scores']['recency'] }}</span>
                                            </div>
                                            <div class="flex items-center justify-between rounded bg-slate-100 px-2 py-1">
                                                <span>Status bonus</span>
                                                <span class="font-semibold">{{ $row['scores']['approval'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 font-semibold">
                                        {{ $row['total'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
