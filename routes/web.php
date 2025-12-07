<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Models\Research;
use App\Models\Field;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\SpkController;

Route::get('/', function () {
    $fieldId = request('field');
    $instId = request('institution');
    $year = request('year');
    $q = trim((string) request('q'));

    $query = Research::select(['id','title','author','field_id','institution_id','year','approved_at'])
        ->with(['institution:id,name', 'field:id,name'])
        ->where('status', 'approved')
        ->orderByDesc('approved_at')
        ->orderByDesc('id');

    if ($fieldId) { $query->where('field_id', $fieldId); }
    if ($instId) { $query->where('institution_id', $instId); }
    if ($year) { $query->where('year', $year); }
    if ($q !== '') {
        $query->where(function($w) use ($q) {
            $w->where('title', 'like', "%$q%")
              ->orWhere('author', 'like', "%$q%");
        });
    }

    $researches = $query->paginate(10)->withQueryString();
    $fields = Field::orderBy('name')->get(['id','name']);

    return view('welcome', compact('researches', 'fields'));
});

// Publik: Berita & Halaman Statis
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::view('/about', 'public.about')->name('about');
Route::view('/contact', 'public.contact')->name('contact');
Route::get('/statistics', [PublicController::class, 'statistics'])->name('public.statistics');
Route::get('/institutions', [PublicController::class, 'institutions'])->name('public.institutions');
Route::view('/guide', 'public.guide')->name('public.guide');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Penelitian (semua user login)
    Route::get('/researches', [ResearchController::class, 'index'])->name('researches.index');
    Route::get('/researches/create', [ResearchController::class, 'create'])->name('researches.create');
    Route::post('/researches', [ResearchController::class, 'store'])->name('researches.store');
    Route::get('/researches/{research}/edit', [ResearchController::class, 'edit'])->name('researches.edit');
    Route::put('/researches/{research}', [ResearchController::class, 'update'])->name('researches.update');
    Route::delete('/researches/{research}', [ResearchController::class, 'destroy'])->name('researches.destroy');
    Route::get('/researches/{research}', [ResearchController::class, 'show'])->name('researches.show');
     
    // Laporan
    Route::get('/reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');

    // SPK otomatis berbasis data yang ada (tidak perlu input manual)
    Route::get('/spk/auto-rank', [SpkController::class, 'autoRank'])->name('spk.auto-rank');

    // Khusus admin
    Route::middleware('isAdmin')->group(function () {
        Route::post('/researches/{research}/approve', [ResearchController::class, 'approve'])->name('researches.approve');
        Route::post('/researches/{research}/reject', [ResearchController::class, 'reject'])->name('researches.reject');
    });

    // Verifikasi oleh Kesbangpol
    Route::post('/researches/{research}/kesbang-verify', [ResearchController::class, 'verifyKesbang'])
        ->name('researches.kesbang.verify');
    Route::post('/researches/{research}/kesbang-reject', [ResearchController::class, 'rejectKesbang'])
        ->name('researches.kesbang.reject');

    // Unggah hasil penelitian (setelah selesai)
    Route::get('/researches/{research}/results', [ResearchController::class, 'editResults'])
        ->name('researches.results.edit');
    Route::post('/researches/{research}/results', [ResearchController::class, 'uploadResults'])
        ->name('researches.results.update');

    // Menu khusus: daftar penelitian saya untuk unggah hasil
    Route::get('/my/results', [ResearchController::class, 'myResults'])
        ->name('researches.results.my');
});

// Admin-only: Kelola Bidang
Route::middleware(['auth','isAdmin'])->group(function () {
    Route::get('/fields', [\App\Http\Controllers\FieldController::class, 'index'])->name('fields.index');
    Route::post('/fields', [\App\Http\Controllers\FieldController::class, 'store'])->name('fields.store');
    Route::patch('/fields/{field}', [\App\Http\Controllers\FieldController::class, 'update'])->name('fields.update');
    Route::delete('/fields/{field}', [\App\Http\Controllers\FieldController::class, 'destroy'])->name('fields.destroy');
});

require __DIR__.'/auth.php';










// Admin routes for complete research data viewing
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/researches', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'index'])
            ->name('researches.index');
        Route::get('/researches/{research}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'show'])
            ->name('researches.show');
        Route::get('/researches/{research}/download/{field}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'download'])
            ->name('researches.download');
        Route::delete('/researches/{research}/file/{field}', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'destroyFile'])
            ->name('researches.file.destroy');
        Route::post('/researches/{research}/remind-results', [\App\Http\Controllers\Admin\ResearchAdminController::class, 'remindResults'])
            ->name('researches.remind-results');
    });

// Auth routes for researchers to download their own files
Route::middleware(['auth'])
    ->group(function () {
        Route::get('/researches/{research}/download/{field}', [\App\Http\Controllers\ResearchDownloadController::class, 'download'])
            ->name('researches.download');
    });

// Super Admin: kelola akun & hak akses
Route::middleware(['auth', 'superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role.update');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.password.reset');
    });
