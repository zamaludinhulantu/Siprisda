<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutoLogout
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $timeoutSeconds = 30 * 60; // 30 menit
            $last = session('last_activity_time');

            if ($last && (time() - (int)$last) > $timeoutSeconds) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['message' => 'Sesi berakhir karena tidak ada aktivitas selama 30 menit.']);
            }

            session(['last_activity_time' => time()]);
        }

        return $next($request);
    }
}

