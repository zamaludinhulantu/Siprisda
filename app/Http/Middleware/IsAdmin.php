<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasAdminAccess()) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya admin yang bisa mengakses halaman ini.');
    }
}
