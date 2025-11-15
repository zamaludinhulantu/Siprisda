<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;
use App\Models\Institution;

class PublicController extends Controller
{
    public function statistics()
    {
        $perField = Field::withCount(['researches' => function($q){
            $q->where('status','approved');
        }])->orderBy('name')->get();

        $perYear = Research::selectRaw('year, COUNT(*) as total')
            ->where('status', 'approved')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return view('public.statistics', compact('perField', 'perYear'));
    }

    public function institutions()
    {
        $institutions = Institution::withCount(['researches' => function($q){
            $q->where('status','approved');
        }])->orderBy('name')->get();

        return view('public.institutions', compact('institutions'));
    }
}

