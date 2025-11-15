<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResearchAdminController extends Controller
{
    /**
     * Display a listing of the research records for admins.
     */
    public function index()
    {
        // Hanya tampilkan yang sudah diverifikasi oleh Kesbangpol
        $researches = Research::query()
            ->whereNotNull('kesbang_verified_at')
            ->latest()
            ->paginate(15);

        return view('admin.researches.index', compact('researches'));
    }

    /**
     * Display the specified research record with complete attributes.
     */
    public function show(Research $research)
    {
        if (is_null($research->kesbang_verified_at)) {
            abort(403, 'Data belum diverifikasi Kesbangpol.');
        }
        return view('admin.researches.show', compact('research'));
    }

    /**
     * Download a file attribute from a research record (admin access).
     */
    public function download(Research $research, string $field)
    {
        if (is_null($research->kesbang_verified_at)) {
            abort(403, 'Data belum diverifikasi Kesbangpol.');
        }
        $value = data_get($research, $field);

        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');

        // Try common disks/paths safely
        if (str_starts_with($path, 'storage/')) {
            $relative = substr($path, strlen('storage/'));
            if (Storage::disk('public')->exists($relative)) {
                return Storage::disk('public')->download($relative);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        if (Storage::exists($path)) {
            return Storage::download($path);
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
        if (is_null($research->kesbang_verified_at)) {
            abort(403, 'Data belum diverifikasi Kesbangpol.');
        }
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
}
