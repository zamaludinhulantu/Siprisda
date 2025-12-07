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
    $isOwner = auth()->check() && (int) auth()->id() === (int) ($research->submitted_by ?? $research->user_id ?? 0);
    $canUploadResults = $isOwner;
    $canUploadAfterKesbang = $canUploadResults && (bool) $research->kesbang_verified_at;
    $waitingKesbangForUpload = $canUploadResults && !$research->kesbang_verified_at;
    $canModerate = $user?->hasAdminAccess();
    $canVerifyKesbang = $user?->hasKesbangAccess();
    $canRejectKesbang = $canVerifyKesbang && !in_array($research->status, ['approved', 'rejected'], true);
    $canReverifyKesbang = $canVerifyKesbang && $research->status === 'rejected';
    $canFixRejected = $isOwner && $research->status === 'rejected';
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
                    @if($research->rejection_message)
                        @php
                            $rejector = optional($research->rejectedBy);
                            $rejectorRole = $rejector?->hasKesbangAccess() ? 'Kesbangpol' : ($rejector?->hasAdminAccess() ? 'Bappeda' : 'Admin');
                            $rejectTime = optional($research->rejected_at)->format('d M Y H:i');
                        @endphp
                        <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs text-rose-700 max-w-sm">
                            <div class="font-semibold flex items-center gap-1">
                                <i class="fas fa-circle-xmark text-[11px]"></i>
                                <span>Pengajuan ditolak</span>
                            </div>
                            <p class="mt-1 text-rose-700/90">{{ $research->rejection_message }}</p>
                            <p class="mt-2 text-[11px] text-rose-600">Ditolak oleh {{ $rejectorRole }} {{ $rejectTime ? 'pada ' . $rejectTime : '' }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                @if($research->kesbang_letter_path)
                    <a href="{{ route('researches.download', [$research, 'kesbang_letter_path']) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                        <i class="fas fa-file-download text-[11px]"></i> Surat Rekomendasi
                    </a>
                @endif
                @if($canUploadAfterKesbang)
                    <a href="{{ route('researches.results.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                        <i class="fas fa-file-upload text-[11px]"></i> Unggah Hasil Penelitian
                    </a>
                @endif
                @if($waitingKesbangForUpload)
                    <span class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-700">
                        <i class="fas fa-hourglass-half text-[11px]"></i> Menunggu ACC Kesbangpol untuk unggah hasil
                    </span>
                @endif
                @if($canVerifyKesbang)
                    <div class="flex flex-col sm:flex-row gap-3">
                        @if(!$research->kesbang_verified_at || $canReverifyKesbang)
                            <form action="{{ route('researches.kesbang.verify', $research->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-500">
                                    <i class="fas fa-shield-check text-[11px]"></i> {{ $canReverifyKesbang ? 'Verifikasi Ulang' : 'Verifikasi Kesbangpol' }}
                                </button>
                            </form>
                        @endif
                        @if($canRejectKesbang)
                            <form action="{{ route('researches.kesbang.reject', $research->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2" data-reject-form>
                                @csrf
                                <input type="hidden" name="rejection_message" value="">
                                <input type="hidden" name="decision_note" value="">
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500" data-reject-trigger>
                                    <i class="fas fa-ban text-[11px]"></i> Tolak Kesbangpol
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
                @if($canFixRejected)
                    <a href="{{ route('researches.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-white">
                        <i class="fas fa-pen text-[11px]"></i> Perbaiki & Ajukan Ulang
                    </a>
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
                        @if($research->status === 'rejected')
                            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                Pengajuan ini sebelumnya ditolak. Anda dapat menyetujui kembali jika peneliti sudah memperbaiki data.
                            </div>
                        @endif
                        <div class="flex flex-col gap-4 md:flex-row">
                            <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                                    <i class="fas fa-check-circle text-[13px]"></i> {{ $research->status === 'rejected' ? 'Setujui Kembali' : 'Setujui' }}
                                </button>
                            </form>
                            <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="flex-1 flex flex-col gap-3" data-reject-form>
                                @csrf
                                <input type="hidden" name="rejection_message" value="">
                                <input type="hidden" name="decision_note" value="">
                                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-500" data-reject-trigger>
                                    <i class="fas fa-times-circle text-[13px]"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    @if($research->status === 'rejected')
                        <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Pengajuan ditolak. Silakan perbaiki data lalu ajukan ulang melalui tombol di atas.
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Menunggu keputusan admin. Anda akan mendapat notifikasi otomatis.</p>
                    @endif
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
                                <th class="px-6 py-3 text-left">Kolom</th>
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
                                $labelMap = [
                                    'id' => 'ID',
                                    'title' => 'Judul',
                                    'author' => 'Peneliti',
                                    'researcher_nik' => 'NIK Peneliti',
                                    'researcher_phone' => 'Kontak Peneliti',
                                    'researcher_email' => 'Email Peneliti',
                                    'institution_id' => 'ID Institusi',
                                    'field_id' => 'ID Bidang',
                                    'year' => 'Tahun',
                                    'start_date' => 'Tanggal Mulai',
                                    'end_date' => 'Tanggal Selesai',
                                    'abstract' => 'Abstrak',
                                    'keywords' => 'Kata Kunci',
                                    'pdf_path' => 'Berkas Proposal',
                                    'kesbang_letter_path' => 'Surat Rekomendasi',
                                    'status' => 'Status',
                                    'submitted_by' => 'Diajukan Oleh',
                                    'submitted_at' => 'Diajukan Pada',
                                    'kesbang_verified_at' => 'Diverifikasi Kesbang Pada',
                                    'kesbang_verified_by' => 'Diverifikasi Kesbang Oleh',
                                    'approved_at' => 'Disetujui Pada',
                                    'results_uploaded_at' => 'Hasil Diunggah Pada',
                                    'rejected_at' => 'Ditolak Pada',
                                    'rejection_message' => 'Alasan Penolakan',
                                    'approved_by' => 'Disetujui Oleh',
                                    'rejected_by' => 'Ditolak Oleh',
                                    'created_at' => 'Dibuat Pada',
                                    'updated_at' => 'Diperbarui Pada',
                                ];
                                $label = $labelMap[$key] ?? \Illuminate\Support\Str::of($key)->replace('_',' ')->title();
                                $display = $value;
                                $badgeClass = null;
                                if (is_null($display) || $display === '') {
                                    $display = '-';
                                } elseif (in_array($key, ['created_at','updated_at','submitted_at','approved_at','rejected_at','kesbang_verified_at','results_uploaded_at']) && $value) {
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Modal satu pintu untuk semua penolakan
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4';
            modal.innerHTML = `
                <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3">
                        <div class="flex items-center gap-2 text-rose-600">
                            <i class="fas fa-ban text-sm"></i>
                            <p class="text-sm font-semibold">Alasan penolakan</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" data-reject-close>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <p class="text-xs text-gray-600">Tuliskan catatan singkat agar peneliti tahu apa yang perlu diperbaiki.</p>
                        <textarea rows="3" class="w-full rounded-lg border border-rose-200 focus:border-rose-500 focus:ring-rose-500 text-sm" data-reject-text placeholder="Contoh: Dokumen rekomendasi masih kosong."></textarea>
                        <p class="text-[11px] text-gray-500">Catatan ini tersimpan bersama keputusan.</p>
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-gray-100 px-5 py-3 bg-gray-50/80 rounded-b-2xl">
                        <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800" data-reject-close>Batal</button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500" data-reject-confirm>
                            <i class="fas fa-paper-plane text-[12px]"></i> Kirim penolakan
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            let activeRejectForm = null;
            const textArea = modal.querySelector('[data-reject-text]');
            const closeButtons = modal.querySelectorAll('[data-reject-close]');
            const confirmBtn = modal.querySelector('[data-reject-confirm]');

            const openModal = (form) => {
                activeRejectForm = form;
                textArea.value = '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                textArea.focus();
            };
            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                activeRejectForm = null;
            };

            closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            confirmBtn.addEventListener('click', () => {
                if (!activeRejectForm) return;
                const message = textArea.value.trim();
                if (!message) {
                    textArea.focus();
                    textArea.classList.add('ring-1', 'ring-rose-500');
                    return;
                }
                textArea.classList.remove('ring-1', 'ring-rose-500');
                let field = activeRejectForm.querySelector('input[name="rejection_message"]');
                if (!field) {
                    field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = 'rejection_message';
                    activeRejectForm.appendChild(field);
                }
                field.value = message;
                let noteField = activeRejectForm.querySelector('input[name="decision_note"]');
                if (!noteField) {
                    noteField = document.createElement('input');
                    noteField.type = 'hidden';
                    noteField.name = 'decision_note';
                    activeRejectForm.appendChild(noteField);
                }
                noteField.value = message;
                activeRejectForm.submit();
                closeModal();
            });

            document.querySelectorAll('[data-reject-trigger]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form');
                    if (form) openModal(form);
                });
            });
        });
    </script>
</x-app-layout>
