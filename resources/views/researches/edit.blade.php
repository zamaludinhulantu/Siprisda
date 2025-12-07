<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Riset Saya</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Perbarui Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Perbaiki data pengajuan sebelum disetujui admin.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Pengajuan</h3>
            <p class="mt-2 text-sm text-gray-600">
                Data dengan status
                <span class="font-semibold text-amber-600">Diajukan/Draft</span>
                atau <span class="font-semibold text-rose-600">Ditolak</span> bisa diperbaiki dan diajukan ulang.
            </p>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
            <form action="{{ route('researches.update', $research->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Judul Penelitian</label>
                        <input type="text" name="title" value="{{ old('title', $research->title) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Penulis / Peneliti</label>
                        <input type="text" name="author" value="{{ old('author', $research->author) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('author')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">NIK Peneliti</label>
                        <input type="text" name="researcher_nik" value="{{ old('researcher_nik', $research->researcher_nik) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('researcher_nik')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="researcher_phone" value="{{ old('researcher_phone', $research->researcher_phone) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('researcher_phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                        <select name="field_id" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">Pilih bidang</option>
                            @foreach ($fields as $field)
                                <option value="{{ $field->id }}" @selected(old('field_id', $research->field_id) == $field->id)>{{ $field->name }}</option>
                            @endforeach
                        </select>
                        @error('field_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Institusi</label>
                        <input type="text" name="institution_name" value="{{ old('institution_name', optional($research->institution)->name) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('institution_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year', $research->year) }}" min="2000" max="{{ date('Y') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('year')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:col-span-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ old('start_date', optional($research->start_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('start_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ old('end_date', optional($research->end_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('end_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Proposal Penelitian (opsional)</label>
                        <input type="file" name="pdf_file" accept="application/pdf" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        @if($research->pdf_path)
                            <p class="text-xs text-gray-500 mt-1">Berkas saat ini:
                                <a href="{{ route('researches.download', [$research, 'pdf_path']) }}" class="text-orange-600 hover:underline">unduh proposal</a>
                            </p>
                        @endif
                        @error('pdf_file')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-save text-xs"></i> Simpan Perubahan
                    </button>
                    <p class="text-xs text-gray-500">Perubahan akan mengirim ulang pengajuan untuk ditinjau admin.</p>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
