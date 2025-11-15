@php
    $attributes = $research->getAttributes();
    $fileLike = collect($attributes)->filter(fn($v) => is_string($v) && preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $v));
    $status = (string)($research->status ?? 'draft');
    $statusMap = [
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
        'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
        'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
        'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['default'];
    $user = auth()->user();
    $canUploadResults = auth()->check() && auth()->id() === ($research->submitted_by ?? $research->user_id ?? null);
    $canModerate = $user?->hasAdminAccess();
    $canVerifyKesbang = $user?->hasKesbangAccess();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Detail</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Detail Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pantau status dan unduh berkas penelitian Anda.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase font-semibold text-gray-500">Judul Penelitian</p>
                    <h1 class="text-xl font-semibold text-gray-900 mt-1">{{ $research->title ?? '-' }}</h1>
                    <p class="text-sm text-gray-500 mt-2">Penulis: {{ $research->author ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Bidang: {{ optional($research->field)->name ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Institusi: {{ optional($research->institution)->name ?? '-' }}</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    <p class="text-xs text-gray-500">Diajukan: {{ optional($research->created_at)->format('d M Y H:i') ?? '-' }}</p>
                    @if($research->status === 'rejected' && $research->rejection_message)
                        <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700 max-w-sm">
                            <strong>Catatan Admin:</strong>
                            <p>{{ $research->rejection_message }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                @if($canUploadResults)
                    <a href="{{ route('researches.results.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                        <i class="fas fa-file-upload text-[11px]"></i> Unggah Hasil Penelitian
                    </a>
                @endif
                @if($canVerifyKesbang && !$research->kesbang_verified_at)
                    <form action="{{ route('researches.kesbang.verify', $research->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-500">
                            <i class="fas fa-shield-check text-[11px]"></i> Verifikasi Kesbangpol
                        </button>
                    </form>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aksi Admin</h3>
                <p class="text-xs text-gray-500">
                    @if($research->kesbang_verified_at)
                        Kesbangpol: <span class="font-semibold text-emerald-600">Sudah diverifikasi</span>
                    @else
                        Kesbangpol: <span class="font-semibold text-amber-600">Belum diverifikasi</span>
                    @endif
                </p>
            </div>
            <div class="px-6 py-5">
                @if($canModerate)
                    @if(!$research->kesbang_verified_at)
                        <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            Menunggu verifikasi Kesbangpol sebelum dapat di-approve atau ditolak.
                        </div>
                    @else
                        <div class="flex flex-col gap-4 md:flex-row">
                            <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                                    <i class="fas fa-check-circle text-[13px]"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="flex-1 flex flex-col gap-3">
                                @csrf
                                <input type="text" name="rejection_message" class="rounded-xl border-gray-200 focus:border-rose-500 focus:ring-rose-500" placeholder="Alasan penolakan" required>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-500">
                                    <i class="fas fa-times-circle text-[13px]"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Menunggu keputusan admin. Anda akan mendapat notifikasi otomatis.</p>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/90 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Berkas Terunggah</h3>
            @if($fileLike->isEmpty())
                <p class="mt-2 text-sm text-gray-500">Tidak ada berkas yang terdeteksi.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left">Field</th>
                                <th class="px-6 py-3 text-left">Nama Berkas</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($fileLike as $key => $val)
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-6 py-4 text-gray-700">{{ \Illuminate\Support\Str::of($key)->replace('_',' ')->title() }}</td>
                                    <td class="px-6 py-4 text-gray-800 break-all">{{ basename($val) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white" href="{{ route('researches.download', [$research, $key]) }}">
                                            <i class="fas fa-download text-[11px]"></i> Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Semua Atribut</h3>
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
                                        <a class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white" href="{{ route('researches.download', [$research, 'pdf_path']) }}">
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
    </div>
</x-app-layout>
