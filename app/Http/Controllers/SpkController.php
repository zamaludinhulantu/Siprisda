<?php

namespace App\Http\Controllers;

use App\Models\Research;
use App\Services\AutoSawRanker;
use Illuminate\Support\Facades\Auth;

class SpkController extends Controller
{
    /**
     * SPK otomatis (SAW) berbasis data yang sudah ada, tanpa input manual.
     */
    public function autoRank()
    {
        if (!config('spk.auto_rank_enabled')) {
            abort(404);
        }

        $user = Auth::user();
        if (!$user || !$user->hasAdminAccess()) {
            abort(403, 'Hanya admin atau superadmin yang dapat mengakses fitur SPK.');
        }

        $researches = Research::select([
                'id',
                'title',
                'author',
                'year',
                'status',
                'field_id',
                'institution_id',
                'pdf_path',
                'kesbang_letter_path',
                'start_date',
                'end_date',
                'updated_at',
            ])
            ->with(['field:id,name', 'institution:id,name'])
            ->orderByDesc('updated_at')
            ->get();

        $ranker = new AutoSawRanker(
            config('spk.auto_rank.weights', []),
            config('spk.auto_rank.status_scores', []),
            [
                'rpjmd_keywords' => config('spk.auto_rank.rpjmd_keywords', []),
                'urgency_keywords' => config('spk.auto_rank.urgency_keywords', []),
                'impact_mapping' => config('spk.auto_rank.impact_mapping', []),
                'documents' => config('spk.auto_rank.documents', []),
                'allowed_pdf_extensions' => config('spk.auto_rank.allowed_pdf_extensions', []),
                'allowed_image_extensions' => config('spk.auto_rank.allowed_image_extensions', []),
            ]
        );

        $results = $ranker->rank($researches);

        return view('spk.auto_rank', [
            'results' => $results,
            'weights' => $ranker->weights(),
            'statusScores' => config('spk.auto_rank.status_scores', []),
            'documents' => config('spk.auto_rank.documents', []),
        ]);
    }
}
