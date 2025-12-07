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

        // Normalize to integers to avoid strict type mismatches from DB string IDs
        $isOwner = (int) ($research->submitted_by ?? 0) === (int) $user->id;
        $hasPower = $user->hasAdminAccess() || $user->hasKesbangAccess();

        if (!$hasPower && !$isOwner) {
            abort(403);
        }

        $value = data_get($research, $field);
        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');

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

        if (preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $path)) {
            return redirect()->to(asset($value));
        }

        abort(404);
    }
}
