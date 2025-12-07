<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Field;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResearchController extends Controller
{
    /**
     * Menampilkan daftar penelitian
     */
    public function index()
    {
        $query = Research::select([
                'id',
                'title',
                'author',
                'institution_id',
                'field_id',
                'status',
                'year',
                'start_date',
                'end_date',
                'created_at',
                'submitted_at',
                'researcher_nik',
                'researcher_phone',
                'kesbang_letter_path',
            ])
            ->with(['institution:id,name', 'field:id,name'])
            ->latest();

        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin'])) {
                $query->where('submitted_by', $user->id);
            }
        }

        $researches = $query->paginate(15);
        return view('researches.index', compact('researches'));
    }

    /**
     * Menampilkan detail penelitian
     */
    public function show(Research $research)
    {
        if (!auth()->check()) {
            abort(403, 'Anda tidak berhak mengakses penelitian ini.');
        }

        $user = auth()->user();
        if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin']) && (int) $research->submitted_by !== (int) $user->id) {
            abort(403, 'Anda tidak berhak mengakses penelitian ini.');
        }

        $research->loadMissing(['rejectedBy']);
        return view('researches.show', compact('research'));
    }

    /**
     * Form edit penelitian
     */
    public function edit(Research $research)
    {
        $this->ensureEditableByOwner($research);
        $fields = Field::all();
        return view('researches.edit', compact('research', 'fields'));
    }

    /**
     * Update penelitian
     */
    public function update(Request $request, Research $research)
    {
        $this->ensureEditableByOwner($research);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'researcher_nik' => 'required|string|min:8|max:32',
            'researcher_phone' => 'required|string|max:32',
            'field_id' => 'required|exists:fields,id',
            'institution_name' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $institution = Institution::firstOrCreate([
            'name' => trim($request->institution_name),
        ]);

        $wasRejected = $research->status === 'rejected';

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'researcher_nik' => $request->researcher_nik,
            'researcher_phone' => $request->researcher_phone,
            'field_id' => $request->field_id,
            'institution_id' => $institution->id,
            'year' => $request->year,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        $pendingDeletion = [];

        if ($request->hasFile('pdf_file')) {
            $newPdfPath = $this->storeFile($request->file('pdf_file'), 'penelitian', 'lampiran-penelitian');
            if ($newPdfPath) {
                $data['pdf_path'] = $newPdfPath;
                if ($research->pdf_path) {
                    $pendingDeletion[] = $research->pdf_path;
                }
            }
        }

        if ($wasRejected) {
            // Reset siklus approval dan kosongkan surat rekomendasi lama
            if ($research->kesbang_letter_path) {
                $pendingDeletion[] = $research->kesbang_letter_path;
            }
            $data = array_merge($data, [
                'status' => 'submitted',
                'submitted_at' => now(),
                'approved_at' => null,
                'approved_by' => null,
                'decision_note' => null,
                'resubmitted_after_reject_at' => now(),
                'kesbang_letter_path' => null,
            ]);
        }

        $research->update($data);

        foreach ($pendingDeletion as $path) {
            $this->deleteIfExists($path);
        }

        $message = $wasRejected
            ? 'Perubahan disimpan dan pengajuan dikirim ulang ke admin.'
            : 'Penelitian berhasil diperbarui.';

        return redirect()->route('researches.index')->with('success', $message);
    }

    /**
     * Menyetujui penelitian (admin saja)
     */
    public function approve(Request $request, Research $research)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menyetujui penelitian.');
        }

        if (is_null($research->kesbang_verified_at)) {
            abort(422, 'Tidak dapat menyetujui: belum diverifikasi Kesbangpol.');
        }

        $request->validate([
            'decision_note' => 'nullable|string|max:2000',
        ]);

        $wasRejected = $research->status === 'rejected';
        $note = $request->decision_note;

        $research->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'decision_note' => $note,
            'rejection_message' => null,
            'rejected_at' => null,
            'rejected_by' => null,
            'resubmitted_after_reject_at' => null,
        ]);

        return redirect()->back()->with('success', $wasRejected ? 'Penelitian disetujui kembali setelah perbaikan.' : 'Penelitian disetujui.');
    }

    /**
     * Menolak penelitian (admin saja)
     */
    public function reject(Request $request, Research $research)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menolak penelitian.');
        }

        if (is_null($research->kesbang_verified_at)) {
            abort(422, 'Tidak dapat menolak: belum diverifikasi Kesbangpol.');
        }

        $request->validate([
            'rejection_message' => 'required|string|max:2000',
            'decision_note' => 'nullable|string|max:2000',
        ]);

        $research->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_message' => $request->rejection_message,
            'decision_note' => $request->decision_note ?? $request->rejection_message,
            'resubmitted_after_reject_at' => null,
        ]);

        return redirect()->back()->with('error', 'Penelitian ditolak.');
    }

    /**
     * Form tambah penelitian
     */
    public function create()
    {
        if (auth()->user()->role === 'kesbangpol') {
            abort(403, 'Akses read-only untuk Kesbangpol.');
        }
        $fields = Field::all();
        return view('researches.create', compact('fields'));
    }

    /**
     * Simpan penelitian baru
     */
    public function store(Request $request)
    {
        if (auth()->user()->role === 'kesbangpol') {
            abort(403, 'Akses read-only untuk Kesbangpol.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'researcher_nik' => 'required|string|min:8|max:32',
            'researcher_phone' => 'required|string|max:32',
            'field_id' => 'required|exists:fields,id',
            'institution_name' => 'required|string|max:255',
            'year' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $institution = Institution::firstOrCreate([
            'name' => trim($request->institution_name),
        ]);

        $pdfPath = '';
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $this->storeFile($request->file('pdf_file'), 'penelitian', 'lampiran-penelitian');
        }

        Research::create([
            'title' => $request->title,
            'author' => $request->author,
            'researcher_nik' => $request->researcher_nik,
            'researcher_phone' => $request->researcher_phone,
            'field_id' => $request->field_id,
            'institution_id' => $institution->id,
            'year' => $request->year,
            'abstract' => $request->abstract,
            'keywords' => $request->keywords,
            'pdf_path' => $pdfPath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'submitted',
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('researches.index')->with('success', 'Penelitian berhasil diunggah!');
    }

    /**
     * Konfirmasi verifikasi oleh Kesbangpol
     */
    public function verifyKesbang(Research $research)
    {
        $user = auth()->user();
        if (!$user || !$user->hasKesbangAccess()) {
            abort(403, 'Akses ditolak. Hanya Kesbangpol yang dapat memverifikasi.');
        }

        if ($research->status === 'approved') {
            abort(422, 'Pengajuan sudah diputuskan Bappeda.');
        }

        $alreadyVerified = (bool) $research->kesbang_verified_at;
        $wasRejected = $research->status === 'rejected';

        $research->update([
            'status' => 'kesbang_verified',
            'kesbang_verified_at' => $wasRejected ? now() : ($research->kesbang_verified_at ?? now()),
            'kesbang_verified_by' => $user->id,
            'rejection_message' => $wasRejected ? null : $research->rejection_message,
            'rejected_at' => $wasRejected ? null : $research->rejected_at,
            'rejected_by' => $wasRejected ? null : $research->rejected_by,
            'decision_note' => $wasRejected ? null : $research->decision_note,
        ]);

        $message = $wasRejected
            ? 'Pengajuan dikembalikan dan diverifikasi ulang oleh Kesbangpol.'
            : ($alreadyVerified
                ? 'Data sudah diverifikasi Kesbangpol.'
                : 'Data diverifikasi oleh Kesbangpol dan diteruskan ke Bappeda.');

        return back()->with('success', $message);
    }

    /**
     * Penolakan oleh Kesbangpol dengan alasan.
     */
    public function rejectKesbang(Request $request, Research $research)
    {
        $user = auth()->user();
        if (!$user || !$user->hasKesbangAccess()) {
            abort(403, 'Akses ditolak. Hanya Kesbangpol yang dapat menolak.');
        }

        if (in_array($research->status, ['approved', 'rejected'], true)) {
            abort(422, 'Pengajuan sudah diputuskan Bappeda.');
        }

        $request->validate([
            'rejection_message' => 'required|string|max:2000',
            'decision_note' => 'nullable|string|max:2000',
        ]);

        $research->update([
            'status' => 'rejected',
            'rejection_message' => $request->rejection_message,
            'decision_note' => $request->decision_note ?? $request->rejection_message,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'kesbang_verified_at' => now(),
            'kesbang_verified_by' => $user->id,
            'resubmitted_after_reject_at' => null,
        ]);

        return back()->with('error', 'Pengajuan ditolak oleh Kesbangpol.');
    }

    /**
     * Unggah hasil penelitian (setelah waktu penelitian selesai)
     */
    public function uploadResults(Request $request, Research $research)
    {
        $this->ensureResultsUploadable($research);

        $request->validate([
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ]);

        $data = [
            'abstract' => $request->abstract ?? $research->abstract,
            'keywords' => $request->keywords ?? $research->keywords,
            'results_uploaded_at' => now(),
        ];

        if ($request->hasFile('pdf_file')) {
            $uploaded = $request->file('pdf_file');
            $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($uploaded->getClientOriginalExtension());
            $safeBase = Str::slug($originalName);
            $fileName = $safeBase . '-' . now()->format('Ymd-His') . '.' . $extension;
            $directory = 'penelitian';
            $data['pdf_path'] = $uploaded->storeAs($directory, $fileName, 'public');
        }

        $research->update($data);

        return back()->with('success', 'Hasil penelitian berhasil diunggah.');
    }

    /**
     * Halaman terpisah untuk unggah hasil penelitian
     */
    public function editResults(Research $research)
    {
        $this->ensureResultsUploadable($research);

        return view('researches.results', compact('research'));
    }

    /**
     * Daftar penelitian milik user untuk unggah hasil
     */
    public function myResults()
    {
        if (!auth()->check()) { abort(403); }
        $researches = Research::where('submitted_by', auth()->id())
            ->orderByDesc('id')
            ->with(['institution:id,name', 'field:id,name'])
            ->get();
        return view('researches.results_index', compact('researches'));
    }

    /**
     * Hapus penelitian oleh pemilik
     */
    public function destroy(Research $research)
    {
        $this->ensureEditableByOwner($research);
        $this->deleteIfExists($research->pdf_path);
        $this->deleteIfExists($research->kesbang_letter_path);
        $research->delete();

        return redirect()->route('researches.index')->with('success', 'Penelitian berhasil dihapus.');
    }

    /**
     * Pastikan user berhak mengubah/ menghapus.
     */
    protected function ensureEditableByOwner(Research $research): void
    {
        if (!auth()->check() || (int) auth()->id() !== (int) ($research->submitted_by ?? 0)) {
            abort(403, 'Anda tidak dapat mengubah penelitian ini.');
        }

        $editableStatuses = ['draft', 'submitted', 'rejected'];
        if (!in_array((string) $research->status, $editableStatuses, true)) {
            abort(403, 'Penelitian yang sudah diproses tidak dapat diubah atau dihapus.');
        }

        if ($research->status !== 'rejected' && $research->kesbang_verified_at) {
            abort(403, 'Penelitian yang sudah diproses tidak dapat diubah atau dihapus.');
        }
    }

    /**
     * Pastikan hasil hanya bisa diunggah setelah disetujui Kesbangpol.
     */
    protected function ensureResultsUploadable(Research $research): void
    {
        if (!auth()->check() || (int) auth()->id() !== (int) ($research->submitted_by ?? 0)) {
            abort(403, 'Anda tidak berhak mengunggah hasil untuk penelitian ini.');
        }

        if (!$research->kesbang_verified_at) {
            abort(403, 'Unggah hasil tersedia setelah disetujui Kesbangpol.');
        }

        if ($research->status === 'rejected') {
            throw new HttpResponseException(
                redirect()->route('researches.results.my')
                    ->with('error', 'Pengajuan yang ditolak tidak dapat mengunggah hasil.')
            );
        }
    }

    /**
     * Hapus file lama jika ada.
     */
    protected function deleteIfExists(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($normalized)) {
            Storage::disk('public')->delete($normalized);
            return;
        }

        if (Storage::exists($normalized)) {
            Storage::delete($normalized);
        }
    }

    /**
     * Simpan file terunggah dengan nama aman.
     */
    protected function storeFile(UploadedFile $file, string $directory, string $fallbackBase = 'lampiran'): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $safeBase = Str::slug($originalName) ?: $fallbackBase;
        $fileName = $safeBase . '-' . now()->format('Ymd-His') . '.' . $extension;
        $relativePath = $directory . '/' . $fileName;

        while (Storage::disk('public')->exists($relativePath)) {
            $fileName = $safeBase . '-' . now()->format('Ymd-His') . '-' . Str::random(6) . '.' . $extension;
            $relativePath = $directory . '/' . $fileName;
        }

        return $file->storeAs($directory, $fileName, 'public');
    }
}
