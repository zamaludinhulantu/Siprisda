<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResearchDownloadController extends Controller
{
    /**
     * Download a file attribute from a research record.
     * Researchers can only download their own; admins can download any.
     */
    public function download(Research $research, string $field)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if (!$user->hasAdminAccess() && ($research->submitted_by ?? null) !== $user->id) {
            abort(403);
        }

        $value = data_get($research, $field);
        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');

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

        if (preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $path)) {
            return redirect()->to(asset($value));
        }

        abort(404);
    }
}
