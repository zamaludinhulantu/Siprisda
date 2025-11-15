@php
    $attributes = $research->getAttributes();
    $fileCandidates = collect($attributes)->filter(fn($v) => is_string($v) && preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $v));
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

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Modul Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Detail Penelitian #{{ $research->id }}</h1>
                <p class="text-sm text-gray-500">Periksa metadata, berkas, dan lakukan keputusan akhir.</p>
            </div>
            <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Judul Penelitian</p>
                    <h2 class="text-xl font-semibold text-gray-900 mt-1">{{ $research->title ?? $research->judul ?? '-' }}</h2>
                    <p class="text-sm text-gray-500 mt-2">Peneliti: {{ $research->author ?? optional($research->user)->name ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Institusi: {{ optional(optional($research->user)->institution)->name ?? '-' }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    <p class="text-xs text-gray-500">Diperbarui: {{ optional($research->updated_at)->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Atribut Lengkap</h3>
                <span class="text-xs text-gray-500">{{ count($attributes) }} kolom</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Atribut</th>
                            <th class="px-6 py-3 text-left">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($attributes as $key => $value)
                            @php
                                $label = \Illuminate\Support\Str::of($key)->replace('_',' ')->title();
                                $display = $value;
                                $badgeClass = null;
                                if (is_null($display) || $display === '') {
                                    $display = '-';
                                } elseif (in_array($key, ['created_at','updated_at','submitted_at','approved_at','rejected_at','kesbang_verified_at']) && $value) {
                                    try { $display = \Carbon\Carbon::parse($value)->format('d M Y H:i'); } catch (\Exception $e) {}
                                } elseif ($key === 'status') {
                                    $badgeClass = $statusInfo['class'];
                                }
                            @endphp
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="px-6 py-4 text-gray-600">{{ $label }}</td>
                                <td class="px-6 py-4">
                                    @if($key === 'status')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $statusInfo['label'] }}</span>
                                    @elseif($key === 'pdf_path' && $value)
                                        <a class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-800" href="{{ route('admin.researches.download', [$research, 'pdf_path']) }}">
                                            <i class="fas fa-download text-[11px]"></i> Unduh ({{ basename((string)$value) }})
                                        </a>
                                    @else
                                        <span class="text-gray-800 break-words">{{ is_scalar($display) ? $display : json_encode($display) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/90 p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Keputusan Admin</h3>
                    <p class="text-sm text-gray-500">Setelah Kesbangpol memverifikasi, admin dapat menyetujui atau menolak pengajuan.</p>
                </div>
                <div class="text-sm text-gray-500">
                    Status Kesbangpol:
                    @if($research->kesbang_verified_at)
                        <span class="font-semibold text-emerald-600">Sudah diverifikasi</span>
                    @else
                        <span class="font-semibold text-amber-600">Menunggu verifikasi</span>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                @if($research->kesbang_verified_at)
                    <div class="flex flex-col gap-4 md:flex-row">
                        <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                                <i class="fas fa-check-circle text-[13px]"></i> Setujui
                            </button>
                        </form>
                        <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="flex-1 flex flex-col gap-3">
                            @csrf
                            <input type="text" name="rejection_message" class="rounded-xl border-gray-200 focus:border-rose-500 focus:ring-rose-500" placeholder="Alasan penolakan" required>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-500">
                                <i class="fas fa-times-circle text-[13px]"></i> Tolak
                            </button>
                        </form>
                    </div>
                @else
                    <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Menunggu verifikasi Kesbangpol sebelum dapat di-approve atau ditolak.
                    </div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Berkas Terunggah</h3>
                <span class="text-xs text-gray-500">{{ $fileCandidates->count() }} berkas terdeteksi</span>
            </div>
            @if($fileCandidates->isEmpty())
                <p class="px-6 py-4 text-sm text-gray-500">Tidak ada berkas yang teridentifikasi otomatis.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Field</th>
                                <th class="px-6 py-3 text-left">Nama Berkas</th>
                                <th class="px-6 py-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($fileCandidates as $fieldName => $path)
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-6 py-4 text-gray-700">{{ \Illuminate\Support\Str::of($fieldName)->replace('_',' ')->title() }}</td>
                                    <td class="px-6 py-4 text-gray-800 break-all">{{ basename($path) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.researches.download', [$research, $fieldName]) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                                <i class="fas fa-download text-[11px]"></i> Unduh
                                            </a>
                                            <form action="{{ route('admin.researches.file.destroy', [$research, $fieldName]) }}" method="POST" onsubmit="return confirm('Hapus berkas ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    <i class="fas fa-trash text-[11px]"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/90 p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Relasi Terkait</h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-100 p-4 bg-white">
                    <p class="text-xs uppercase font-semibold text-gray-500">Peneliti</p>
                    <p class="text-sm text-gray-900 mt-1">Nama: {{ optional($research->user)->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">Email: {{ optional($research->user)->email ?? '-' }}</p>
                    <p class="text-sm text-gray-600">Institusi: {{ optional(optional($research->user)->institution)->name ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 bg-white">
                    <p class="text-xs uppercase font-semibold text-gray-500">Bidang</p>
                    <p class="text-sm text-gray-900 mt-1">{{ optional($research->field)->name ?? '-' }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-4 bg-white">
                    <p class="text-xs uppercase font-semibold text-gray-500">Review</p>
                    @php $reviews = $research->reviews ?? []; @endphp
                    @forelse($reviews as $review)
                        <div class="mt-2 rounded-lg border border-gray-100 p-3">
                            <p class="text-sm font-semibold text-gray-900">Reviewer: {{ optional($review->reviewer)->name ?? '-' }}</p>
                            @php $reviewAttrs = $review->getAttributes(); @endphp
                            <dl class="mt-2 grid grid-cols-1 gap-2 text-xs text-gray-600">
                                @foreach($reviewAttrs as $rk => $rv)
                                    <div>
                                        <dt class="uppercase tracking-wide">{{ $rk }}</dt>
                                        <dd class="text-gray-800">{{ is_scalar($rv) ? $rv : json_encode($rv) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada review.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
