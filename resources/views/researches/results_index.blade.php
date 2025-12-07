<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hasil Penelitian</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Unggah Hasil Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pilih penelitian yang sudah selesai dan unggah dokumen finalnya.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Daftar Penelitian
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success') || session('error'))
            <div class="space-y-3">
                @if(session('success'))
                    <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <i class="fas fa-circle-check mt-0.5"></i>
                        <div class="font-semibold">{{ session('success') }}</div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <i class="fas fa-circle-exclamation mt-0.5"></i>
                        <div class="font-semibold">{{ session('error') }}</div>
                    </div>
                @endif
            </div>
        @endif

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            @if($researches->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    Anda belum memiliki penelitian yang dapat diunggah hasilnya. Silakan buat pendaftaran terlebih dahulu.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Judul</th>
                                <th class="px-6 py-3 text-left">Bidang</th>
                                <th class="px-6 py-3 text-left">Institusi</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Periode</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($researches as $research)
                                @php
                                    $status = strtoupper($research->status ?? 'DRAFT');
                                    $canUpload = (bool) $research->kesbang_verified_at;
                                @endphp
                                <tr class="hover:bg-orange-50/30 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $research->title }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ optional($research->institution)->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $status }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ optional($research->start_date)->format('d M Y') }} - {{ optional($research->end_date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if($canUpload)
                                            <a href="{{ route('researches.results.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                                <i class="fas fa-upload text-[11px]"></i> Unggah Hasil
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                                <i class="fas fa-hourglass-half text-[11px]"></i> Menunggu ACC Kesbangpol
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
