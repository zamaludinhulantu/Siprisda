<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $query = Research::query();

        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin'])) {
                $query = $query->where('submitted_by', $user->id);
            }
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $recentResearches = (clone $query)
            ->latest('updated_at')
            ->take(6)
            ->get();

        $fields = collect();
        $years = collect();
        if (Auth::check() && Auth::user()->hasRole(['admin', 'superadmin'])) {
            $fields = Field::orderBy('name')->get(['id', 'name']);
            $years = Research::select('year')
                ->whereNotNull('year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year');
        }

        return view('dashboard', compact('total', 'approved', 'rejected', 'submitted', 'recentResearches', 'fields', 'years'));
    }
}
