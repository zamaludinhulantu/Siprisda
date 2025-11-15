<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Models\Field;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                'created_at',
                'submitted_at',
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
        if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin']) && $research->submitted_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses penelitian ini.');
        }

        return view('researches.show', compact('research'));
    }

    /**
     * Menyetujui penelitian (admin saja)
     */
    public function approve(Research $research)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menyetujui penelitian.');
        }

        if (is_null($research->kesbang_verified_at)) {
            abort(422, 'Tidak dapat menyetujui: belum diverifikasi Kesbangpol.');
        }

        $research->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Penelitian disetujui.');
    }

    /**
     * Menolak penelitian (admin saja)
     */
    public function reject(Research $research)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menolak penelitian.');
        }

        if (is_null($research->kesbang_verified_at)) {
            abort(422, 'Tidak dapat menolak: belum diverifikasi Kesbangpol.');
        }

        request()->validate([
            'rejection_message' => 'required|string|max:2000',
        ]);

        $research->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_message' => request('rejection_message'),
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
            'pdf_file' => 'nullable|mimes:pdf|max:20480', // opsional di tahap awal
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        $institution = Institution::firstOrCreate([
            'name' => trim($request->institution_name),
        ]);

        // Simpan file (jika ada) dengan nama asli yang sudah disanitasi, hindari tabrakan nama
        $pdfPath = '';
        if ($request->hasFile('pdf_file')) {
            $uploaded = $request->file('pdf_file');
            $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($uploaded->getClientOriginalExtension());
            $safeBase = Str::slug($originalName);
            $fileName = $safeBase . '.' . $extension;
            $directory = 'penelitian';
            $relativePath = $directory . '/' . $fileName;

            if (Storage::disk('public')->exists($relativePath)) {
                $fileName = $safeBase . '-' . now()->format('Ymd-His') . '.' . $extension;
                $relativePath = $directory . '/' . $fileName;
            }

            $pdfPath = $uploaded->storeAs($directory, $fileName, 'public');
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
        if (!auth()->check() || !auth()->user()->hasKesbangAccess()) {
            abort(403, 'Akses ditolak. Hanya Kesbangpol yang dapat memverifikasi.');
        }

        // Tandai diverifikasi oleh Kesbangpol, tetap status submitted
        $research->update([
            'kesbang_verified_at' => now(),
            'kesbang_verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Data diverifikasi oleh Kesbangpol dan diteruskan ke Bappeda.');
    }

    /**
     * Unggah hasil penelitian (setelah waktu penelitian selesai)
     */
    public function uploadResults(Request $request, Research $research)
    {
        if (!auth()->check() || auth()->id() !== $research->submitted_by) {
            abort(403, 'Anda tidak berhak mengunggah hasil untuk penelitian ini.');
        }

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
        if (!auth()->check() || auth()->id() !== $research->submitted_by) {
            abort(403, 'Anda tidak berhak mengunggah hasil untuk penelitian ini.');
        }

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
}
