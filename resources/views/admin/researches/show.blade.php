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
    $isFinalDecision = in_array($status, ['approved', 'rejected'], true);
    $decisionBadge = $status === 'approved'
        ? ['class' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100', 'icon' => 'fa-circle-check', 'title' => 'Pengajuan disetujui']
        : ['class' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100', 'icon' => 'fa-circle-xmark', 'title' => 'Pengajuan ditolak'];
    $startDate = $research->start_date;
    $endDate = $research->end_date;
    $isInResearchPeriod = $startDate && $endDate && now()->between($startDate, $endDate);
    $minutesLeft = $isInResearchPeriod ? now()->diffInMinutes($endDate, false) : null;
    $hoursLeftInt = !is_null($minutesLeft) ? max(0, (int) ceil($minutesLeft / 60)) : null;
    $daysLeft = !is_null($minutesLeft) ? max(0, (int) ceil($minutesLeft / 1440)) : null;
    $isResearchFinished = $endDate && $endDate->isPast();
    $submitter = optional($research->submitter);
    $contactPhone = $research->researcher_phone ?? $submitter->phone ?? $submitter->phone_number ?? null;
    $contactEmail = $research->researcher_email ?? $submitter->email ?? null;
    $waNumber = $contactPhone ? preg_replace('/\D+/', '', (string)$contactPhone) : null;
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . urlencode('Halo ' . ($research->author ?? $submitter->name ?? 'Peneliti') . ', mohon unggah hasil penelitian melalui tautan berikut: ' . route('researches.results.edit', $research->id)) : null;
@endphp

<x-app-layout>
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                <i class="fas fa-check-circle mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 flex items-start gap-2">
                <i class="fas fa-info-circle mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
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
                    <p class="text-sm text-gray-500 mt-2">Peneliti: {{ $research->author ?? $submitter->name ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Institusi: {{ optional($submitter->institution)->name ?? optional($research->institution)->name ?? '-' }}</p>
                    @if($research->resubmitted_after_reject_at)
                        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                            <i class="fas fa-rotate text-[11px]"></i>
                            Perbaikan setelah ditolak ({{ optional($research->resubmitted_after_reject_at)->format('d M Y H:i') }})
                        </div>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    <p class="text-xs text-gray-500">Diperbarui: {{ optional($research->updated_at)->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
            @if(!$research->kesbang_verified_at)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-start gap-2">
                    <i class="fas fa-hourglass-half mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Menunggu verifikasi Kesbangpol</p>
                        <p class="text-amber-700">Data sudah terlihat oleh Bappeda, namun keputusan akhir hanya bisa dilakukan setelah Kesbangpol memverifikasi.</p>
                    </div>
                </div>
            @endif
            @if($isInResearchPeriod)
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                    <i class="fas fa-circle-play mt-0.5 text-emerald-500"></i>
                    <div>
                        <p class="font-semibold">Sedang dalam masa penelitian</p>
                        <p class="text-emerald-700">Periode aktif hingga {{ optional($endDate)->format('d M Y') }}.</p>
                    </div>
                </div>
            @endif
            @if($isResearchFinished)
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800 flex flex-col gap-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-bullhorn mt-0.5 text-emerald-500"></i>
                        <div>
                            <p class="font-semibold">Hubungi peneliti untuk unggah hasil penelitian</p>
                            <p class="text-emerald-700">Periode telah selesai. Kirim pengingat dan arahkan peneliti mengunggah dokumen akhir.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('researches.results.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800">
                            <i class="fas fa-file-upload text-[11px]"></i> Buka halaman unggah hasil
                        </a>
                        <form action="{{ route('admin.researches.remind-results', $research) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                <i class="fas fa-paper-plane text-[11px]"></i> Kirim email otomatis
                            </button>
                        </form>
                        @if($waLink)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                <i class="fab fa-whatsapp text-[11px]"></i> WhatsApp Peneliti
                            </a>
                        @endif
                        @if($contactEmail)
                            <a href="mailto:{{ $contactEmail }}?subject=Permintaan%20unggah%20hasil%20penelitian&body={{ urlencode('Halo ' . ($research->author ?? $submitter->name ?? 'Peneliti') . ',\n\nMohon unggah hasil penelitian Anda pada tautan berikut: ' . route('researches.results.edit', $research->id) . '\n\nTerima kasih.') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-envelope text-[11px]"></i> Email Peneliti
                            </a>
                        @endif
                        @if(!$waLink && !$contactEmail)
                            <span class="text-xs text-emerald-700">Kontak peneliti belum tersedia.</span>
                        @elseif(!$contactEmail)
                            <span class="text-xs text-emerald-700">Email peneliti belum tersedia, klik tombol akan menampilkan peringatan.</span>
                        @endif
                    </div>
                </div>
            @endif
            @if($research->kesbang_letter_path)
                <div class="mt-4">
                    <a href="{{ route('admin.researches.download', [$research, 'kesbang_letter_path']) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                        <i class="fas fa-file-signature text-[11px]"></i> Unduh Surat Rekomendasi
                    </a>
                </div>
            @endif
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

        <section class="rounded-3xl border border-gray-100 bg-gradient-to-br from-white via-orange-50/40 to-white shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Keputusan Admin</p>
                    <h3 class="text-lg font-semibold text-gray-900">Tentukan hasil pengajuan</h3>
                    <p class="text-sm text-gray-600">Setelah Kesbangpol memverifikasi, admin dapat menyetujui atau menolak pengajuan.</p>
                </div>
                <div class="text-sm text-gray-600 flex items-center gap-2">
                    <span>Status Kesbangpol:</span>
                    @if($research->kesbang_verified_at)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">Sudah diverifikasi</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">Menunggu verifikasi</span>
                    @endif
                </div>
            </div>
            <div class="mt-5">
                @if($research->kesbang_verified_at)
                    @if($isFinalDecision)
                        <div class="rounded-2xl border bg-white p-5 shadow-sm space-y-3 {{ $status === 'approved' ? 'border-emerald-100' : 'border-rose-100' }}">
                            <div class="flex items-start gap-3">
                                <span class="h-12 w-12 shrink-0 rounded-full {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center ring-8 ring-white">
                                    <i class="fas {{ $decisionBadge['icon'] }} text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $decisionBadge['title'] }}</p>
                                    <p class="text-xs text-gray-600">Keputusan final, tombol aksi disembunyikan untuk menghindari dobel-keputusan.</p>
                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold {{ $decisionBadge['class'] }}">{{ $statusInfo['label'] }}</span>
                                        @if($research->approved_at || $research->rejected_at)
                                            <span class="text-gray-600">pada {{ optional($research->approved_at ?? $research->rejected_at)->format('d M Y H:i') }}</span>
                                        @endif
                                        @if($status === 'rejected' && $research->rejection_message)
                                            <span class="text-gray-500">- {{ $research->rejection_message }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50/60 px-4 py-3 text-xs text-gray-600 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-gray-800">Butuh ubah keputusan?</p>
                                    <p class="text-gray-600">Revisi hanya jika ada bukti baru. Catatan perubahan wajib.</p>
                                </div>
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50" data-reopen-trigger>
                                    <i class="fas fa-rotate text-[12px]"></i> Ubah keputusan
                                </button>
                            </div>
                            <div class="hidden rounded-2xl border border-gray-100 bg-gray-50/70 px-4 py-4 space-y-3" data-reopen-panel>
                                <div class="flex items-start gap-2">
                                    <span class="mt-0.5 text-gray-500"><i class="fas fa-info-circle text-[12px]"></i></span>
                                    <p class="text-xs text-gray-600">Pilih keputusan baru dan catat alasan revisi untuk audit trail.</p>
                                </div>
                                <div class="space-y-2">
                                    <label for="decision_note" class="text-xs font-semibold text-gray-700">Catatan perubahan (wajib)</label>
                                    <textarea id="decision_note" name="decision_note" class="w-full rounded-lg border-gray-200 focus:border-gray-500 focus:ring-gray-500 text-sm" rows="2" placeholder="Contoh: Peneliti mengunggah berkas yang kurang, hasil verifikasi ulang valid." disabled></textarea>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm flex flex-col gap-3" data-reopen-action>
                                        @csrf
                                        <input type="hidden" name="decision_note" value="" disabled>
                                        <div class="flex items-start gap-3">
                                            <span class="h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                                <i class="fas fa-check text-sm"></i>
                                            </span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Setujui (revisi)</p>
                                                <p class="text-xs text-gray-600">Publikasikan setelah verifikasi ulang.</p>
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 shadow-md shadow-emerald-100">
                                            <i class="fas fa-check-circle text-[13px]"></i> Simpan keputusan baru
                                        </button>
                                    </form>
                                    <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm flex flex-col gap-3" data-reopen-action>
                                        @csrf
                                        <input type="hidden" name="decision_note" value="" disabled>
                                        <input type="hidden" name="rejection_message" value="" disabled>
                                        <div class="flex items-start gap-3">
                                            <span class="h-10 w-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                                                <i class="fas fa-times text-sm"></i>
                                            </span>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">Tolak (revisi)</p>
                                                <p class="text-xs text-gray-600">Pastikan alasan penolakan diperbarui.</p>
                                            </div>
                                        </div>
                                        <button type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-500 shadow-md shadow-rose-100" data-reject-trigger>
                                            <i class="fas fa-times-circle text-[13px]"></i> Simpan keputusan baru
                                        </button>
                                    </form>
                                </div>
                                <p class="text-[11px] text-gray-500">Catatan perubahan akan dikirim bersama permintaan. Simpan bukti pendukung di log sistem.</p>
                            </div>
                        </div>
                    @else
                        <div class="grid gap-4 md:grid-cols-2">
                            <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm flex flex-col gap-3">
                                @csrf
                                <div class="flex items-start gap-3">
                                    <span class="h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <i class="fas fa-check text-sm"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Setujui Pengajuan</p>
                                        <p class="text-xs text-gray-600">Publikasikan setelah semua berkas dinyatakan valid.</p>
                                    </div>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 shadow-md shadow-emerald-100">
                                    <i class="fas fa-check-circle text-[13px]"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm flex flex-col gap-3" data-reject-form>
                                @csrf
                                <div class="flex items-start gap-3">
                                    <span class="h-10 w-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                                        <i class="fas fa-times text-sm"></i>
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">Tolak Pengajuan</p>
                                        <p class="text-xs text-gray-600">Berikan alasan jelas agar peneliti dapat memperbaiki.</p>
                                    </div>
                                </div>
                                <input type="hidden" name="rejection_message" value="">
                                <input type="hidden" name="decision_note" value="">
                                <button type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-500 shadow-md shadow-rose-100" data-reject-trigger>
                                    <i class="fas fa-times-circle text-[13px]"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-center gap-2">
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                        <span>Menunggu verifikasi Kesbangpol sebelum dapat di-approve atau ditolak.</span>
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
                                <th class="px-6 py-3 text-left">Kolom</th>
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
                    <p class="text-sm text-gray-900 mt-1">Nama: {{ optional($research->submitter)->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">Email: {{ optional($research->submitter)->email ?? '-' }}</p>
                    <p class="text-sm text-gray-600">Institusi: {{ optional(optional($research->submitter)->institution)->name ?? '-' }}</p>
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

            const reopenTrigger = document.querySelector('[data-reopen-trigger]');
            const reopenPanel = document.querySelector('[data-reopen-panel]');
            if (reopenTrigger && reopenPanel) {
                reopenTrigger.addEventListener('click', () => {
                    reopenPanel.classList.remove('hidden');
                    reopenTrigger.classList.add('hidden');
                    const noteField = reopenPanel.querySelector('textarea[name="decision_note"]');
                    if (noteField) {
                        noteField.removeAttribute('disabled');
                        noteField.setAttribute('required', 'required');
                        noteField.focus();
                    }
                    reopenPanel.querySelectorAll('[data-reopen-action]').forEach((form) => {
                        form.querySelectorAll('input,button,textarea').forEach((el) => {
                            el.removeAttribute('disabled');
                        });
                        form.addEventListener('submit', () => {
                            if (noteField) {
                                const hiddenNote = form.querySelector('input[name="decision_note"]');
                                if (hiddenNote) hiddenNote.value = noteField.value;
                            }
                        });
                    });
                });
            }
        });
    </script>
</x-app-layout>


