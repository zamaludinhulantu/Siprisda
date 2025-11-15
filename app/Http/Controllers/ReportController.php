<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;

class ReportController extends Controller
{
    public function statistics()
    {
        $perField = Field::withCount('researches')->orderBy('name')->get();
        $perYear = Research::selectRaw('year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return view('reports.statistics', compact('perField', 'perYear'));
    }
}

