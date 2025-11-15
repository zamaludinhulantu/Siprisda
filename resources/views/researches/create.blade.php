<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Formulir</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Unggah Penelitian Baru') }}</h2>
                <p class="text-sm text-gray-500">Lengkapi informasi inti penelitian. Anda masih dapat memperbarui detail setelah disimpan.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Langkah Pengisian</h3>
            <ol class="mt-4 grid gap-3 text-sm text-gray-600 sm:grid-cols-3">
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">1. Identitas</p>
                    <p>Judul, peneliti, kontak.</p>
                </li>
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">2. Rincian Riset</p>
                    <p>Bidang, institusi, jadwal.</p>
                </li>
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">3. Perizinan</p>
                    <p>Unggah surat rekomendasi dari Kesbangpol.</p>
                </li>
            </ol>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
            <form action="{{ route('researches.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Judul Penelitian</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Penulis / Peneliti</label>
                        <input type="text" name="author" value="{{ old('author') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('author')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">NIK Peneliti</label>
                        <input type="text" name="researcher_nik" value="{{ old('researcher_nik') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('researcher_nik')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="researcher_phone" value="{{ old('researcher_phone') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('researcher_phone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                        <select name="field_id" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            <option value="">Pilih bidang</option>
                            @foreach ($fields as $field)
                                <option value="{{ $field->id }}" @selected(old('field_id') == $field->id)>{{ $field->name }}</option>
                            @endforeach
                        </select>
                        @error('field_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Institusi</label>
                        <input type="text" name="institution_name" value="{{ old('institution_name') }}" placeholder="Nama institusi lengkap" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('institution_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="year" value="{{ old('year') }}" min="2000" max="{{ date('Y') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('year')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:col-span-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('start_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('end_date')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Surat Rekomendasi Kesbangpol</label>
                    <input type="file" name="kesbang_letter" accept="application/pdf,image/*" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                    <p class="text-xs text-gray-500 mt-1">Unggah file PDF atau foto surat rekomendasi resmi.</p>
                    @error('kesbang_letter')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-cloud-upload-alt text-xs"></i> Simpan & Kirim
                    </button>
                    <p class="text-xs text-gray-500">Unggah berkas final melalui menu hasil setelah penelitian selesai.</p>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
