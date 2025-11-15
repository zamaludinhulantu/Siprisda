<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Update Hasil</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Unggah Hasil Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Lampirkan abstrak final, kata kunci, dan berkas PDF terbaru.</p>
            </div>
            <a href="{{ route('researches.show', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Detail Penelitian
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">{{ $research->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">Periode: {{ optional($research->start_date)->format('d M Y') }} - {{ optional($research->end_date)->format('d M Y') }}</p>
            <p class="text-xs text-gray-500 mt-2">Pastikan dokumen sesuai template PDF resmi.</p>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
            <form action="{{ route('researches.results.update', $research->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-700">Abstrak (opsional)</label>
                    <textarea name="abstract" rows="5" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">{{ old('abstract', $research->abstract) }}</textarea>
                    @error('abstract')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Kata Kunci (opsional)</label>
                    <input type="text" name="keywords" value="{{ old('keywords', $research->keywords) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                    @error('keywords')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">File PDF Hasil (opsional)</label>
                    <input type="file" name="pdf_file" accept="application/pdf" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Format PDF, ukuran maksimal 10MB.</p>
                    @error('pdf_file')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-save text-xs"></i> Simpan Pembaruan
                    </button>
                    <span class="text-xs text-gray-500">Perubahan akan ditinjau otomatis oleh admin.</span>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
