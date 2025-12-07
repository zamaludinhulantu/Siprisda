<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ResearchResultReminder;
use App\Models\Field;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ResearchAdminController extends Controller
{
    /**
     * Display a listing of the research records for admins.
     */
    public function index(Request $request)
    {
        $query = Research::query()
            ->with(['field:id,name', 'institution:id,name']);

        // Hanya tampilkan yang sudah diverifikasi Kesbangpol untuk Bappeda
        $query->whereNotNull('kesbang_verified_at');

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('researcher_nik', 'like', "%{$search}%")
                    ->orWhere('researcher_phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($fieldId = $request->input('field_id')) {
            $query->where('field_id', $fieldId);
        }

        if ($year = $request->input('year')) {
            $query->where('year', (int) $year);
        }

        if ($institution = trim((string) $request->input('institution'))) {
            $query->whereHas('institution', function ($builder) use ($institution) {
                $builder->where('name', 'like', '%' . $institution . '%');
            });
        }

        $researches = $query->latest()->paginate(15)->withQueryString();

        $fields = Field::orderBy('name')->get(['id', 'name']);
        $years = Research::select('year')->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.researches.index', compact('researches', 'fields', 'years'));
    }

    /**
     * Display the specified research record with complete attributes.
     */
    public function show(Research $research)
    {
        // Jangan tampilkan ke admin Bappeda jika belum diverifikasi Kesbangpol
        if (is_null($research->kesbang_verified_at)) {
            abort(404);
        }

        $research->load(['submitter', 'institution', 'field', 'rejectedBy']);
        return view('admin.researches.show', compact('research'));
    }

    /**
     * Download a file attribute from a research record (admin access).
     */
    public function download(Research $research, string $field)
    {
        $value = data_get($research, $field);

        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');

        // Try common disks/paths safely
        $inlineResponse = function (string $disk, string $relative) {
            $storage = Storage::disk($disk);
            $absolute = $storage->path($relative);
            $mime = $storage->mimeType($relative) ?? 'application/octet-stream';
            $filename = basename($relative);

            return response()->file($absolute, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        };

        if (str_starts_with($path, 'storage/')) {
            $relative = substr($path, strlen('storage/'));
            if (Storage::disk('public')->exists($relative)) {
                return $inlineResponse('public', $relative);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return $inlineResponse('public', $path);
        }

        if (Storage::exists($path)) {
            $absolute = Storage::path($path);
            $mime = Storage::mimeType($path) ?? 'application/octet-stream';
            $filename = basename($path);

            return response()->file($absolute, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        // Fallback to public asset if looks like public URL
        if (preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $path)) {
            return redirect()->to(asset($value));
        }

        abort(404);
    }

    /**
     * Remove a file attribute from a research record (admin only).
     */
    public function destroyFile(Research $research, string $field)
    {
        $value = data_get($research, $field);

        if ($value && is_string($value)) {
            $path = ltrim($value, '/');

            if (str_starts_with($path, 'storage/')) {
                $relative = substr($path, strlen('storage/'));
                if (Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            } elseif (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            } elseif (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        // Clear the attribute regardless of file presence
        $research->{$field} = null;
        $research->save();

        return back()->with('success', 'Berkas berhasil dihapus.');
    }

    /**
     * Send an email reminder asking the researcher to upload final results.
     */
    public function remindResults(Request $request, Research $research)
    {
        $research->loadMissing('submitter');
        $contactEmail = $research->researcher_email ?? optional($research->submitter)->email;

        if (!$contactEmail) {
            return back()->with('error', 'Email peneliti belum tersedia.');
        }

        Mail::to($contactEmail)->send(new ResearchResultReminder($research));

        return back()->with('success', 'Email pengingat unggah hasil telah dikirim.');
    }
}
