<?php

namespace App\Services;

use App\Models\Research;
use Illuminate\Support\Collection;

class AutoSawRanker
{
    private array $weights;
    private array $statusScores;
    private array $rpjmdKeywords;
    private array $urgencyKeywords;
    private array $impactMapping;
    private array $documents;
    private array $allowedPdfExtensions;
    private array $allowedImageExtensions;

    public function __construct(array $weights = [], array $statusScores = [], array $config = [])
    {
        $this->weights = $this->normalizeWeights($weights);
        $this->statusScores = $statusScores;
        $this->rpjmdKeywords = $config['rpjmd_keywords'] ?? [];
        $this->urgencyKeywords = $config['urgency_keywords'] ?? [];
        $this->impactMapping = $config['impact_mapping'] ?? [];
        $this->documents = $config['documents'] ?? [];
        $this->allowedPdfExtensions = $config['allowed_pdf_extensions'] ?? ['pdf'];
        $this->allowedImageExtensions = $config['allowed_image_extensions'] ?? ['jpg', 'jpeg', 'png'];
    }

    /**
    * @param Collection<int, Research> $researches
    * @return array<int, array{research: Research, scores: array<string,float>, total: float}>
    */
    public function rank(Collection $researches): array
    {
        if ($researches->isEmpty()) {
            return [];
        }

        $yearValues = $researches->pluck('year')
            ->filter()
            ->map(fn ($year) => (int) $year)
            ->values();

        $minYear = $yearValues->min() ?? (int) date('Y');
        $maxYear = $yearValues->max() ?? (int) date('Y');

        $rows = [];

        foreach ($researches as $research) {
            $recencyScore = $this->scoreRecency($research, $minYear, $maxYear);
            $rpjmdScore = $this->scoreKeywords($research, $this->rpjmdKeywords);
            $urgencyScore = $this->scoreKeywords($research, $this->urgencyKeywords);
            $completenessScore = $this->scoreCompleteness($research);
            $impactScore = $this->scoreImpact($research);
            $procedureScore = $this->scoreProcedure($research);
            $approvalScore = $this->statusScores[$research->status] ?? 0.4;

            $total = ($rpjmdScore * $this->weights['rpjmd_relevance'])
                + ($urgencyScore * $this->weights['urgency'])
                + ($completenessScore * $this->weights['completeness'])
                + ($impactScore * $this->weights['impact'])
                + ($procedureScore * $this->weights['procedure']);
            // Approval diikutkan sebagai faktor tambahan ringan (agar tidak mengubah bobot utama).
            $total += $approvalScore * 0.05; // 5% bonus status approval

            $rows[] = [
                'research' => $research,
                'scores' => [
                    'rpjmd_relevance' => round($rpjmdScore, 3),
                    'urgency' => round($urgencyScore, 3),
                    'completeness' => round($completenessScore, 3),
                    'impact' => round($impactScore, 3),
                    'procedure' => round($procedureScore, 3),
                    'recency' => round($recencyScore, 3),
                    'approval' => round($approvalScore, 3),
                ],
                'total' => round($total, 4),
            ];
        }

        usort($rows, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $rows;
    }

    public function weights(): array
    {
        return $this->weights;
    }

    private function scoreRecency(Research $research, int $minYear, int $maxYear): float
    {
        $raw = $research->year ? (int) $research->year : $minYear;
        return $this->normalizeBenefit($raw, $minYear, $maxYear);
    }

    private function normalizeBenefit(int|float $value, int|float $min, int|float $max): float
    {
        if ($max <= $min) {
            return 1.0;
        }

        return ($value - $min) / ($max - $min);
    }

    private function scoreKeywords(Research $research, array $keywords): float
    {
        if (empty($keywords)) {
            return 0.5;
        }

        $haystack = strtolower(trim(
            ($research->title ?? '') . ' ' .
            ($research->keywords ?? '') . ' ' .
            ($research->abstract ?? '') . ' ' .
            (optional($research->field)->name ?? '')
        ));

        $matches = 0;
        foreach ($keywords as $word) {
            if ($word !== '' && str_contains($haystack, strtolower($word))) {
                $matches++;
            }
        }

        return min(1.0, $matches / max(1, count($keywords) / 3)); // normalisasi sederhana
    }

    private function scoreCompleteness(Research $research): float
    {
        if (empty($this->documents)) {
            return 0.0;
        }

        $score = 0.0;
        foreach ($this->documents as $doc => $weight) {
            $weight = is_numeric($weight) ? (float) $weight : 0.0;
            if ($weight <= 0) {
                continue;
            }

            $exists = match ($doc) {
                'proposal' => (bool) $research->pdf_path,
                'surat_permohonan' => (bool) $research->kesbang_letter_path,
                'ktp' => false, // belum tersedia di model
                'surat_rekomendasi' => false, // belum tersedia di model
                default => false,
            };

            if ($exists) {
                $score += $weight;
            }
        }

        $totalWeight = array_sum(array_map(fn ($w) => is_numeric($w) ? (float) $w : 0.0, $this->documents));
        if ($totalWeight <= 0) {
            return 0.0;
        }

        return min(1.0, $score / $totalWeight);
    }

    private function scoreImpact(Research $research): float
    {
        if (empty($this->impactMapping)) {
            return 0.5;
        }

        $fieldName = strtolower(optional($research->field)->name ?? '');
        $title = strtolower($research->title ?? '');
        $maxScore = 0.0;

        foreach ($this->impactMapping as $key => $score) {
            $score = is_numeric($score) ? (float) $score : 0.0;
            if ($score <= 0) {
                continue;
            }
            $key = strtolower($key);
            if ($key === '') {
                continue;
            }
            if (str_contains($fieldName, $key) || str_contains($title, $key)) {
                $maxScore = max($maxScore, min(1.0, $score));
            }
        }

        return $maxScore > 0 ? $maxScore : 0.3;
    }

    private function scoreProcedure(Research $research): float
    {
        $score = 0.0;
        $parts = 0;

        // Format proposal (PDF)
        $parts++;
        $score += $this->hasAllowedExtension($research->pdf_path, $this->allowedPdfExtensions) ? 1 : 0;

        // Format surat permohonan (pdf/jpg/png)
        $parts++;
        $score += $this->hasAllowedExtension($research->kesbang_letter_path, array_merge($this->allowedPdfExtensions, $this->allowedImageExtensions)) ? 1 : 0;

        // Jadwal tersedia (start dan end)
        $parts++;
        $score += ($research->start_date && $research->end_date) ? 1 : 0;

        if ($parts === 0) {
            return 0.0;
        }

        return $score / $parts;
    }

    private function hasAllowedExtension(?string $path, array $allowed): bool
    {
        if (!$path) {
            return false;
        }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, $allowed, true);
    }

    private function normalizeWeights(array $weights): array
    {
        $defaults = [
            'rpjmd_relevance' => 0.30,
            'urgency' => 0.20,
            'completeness' => 0.20,
            'impact' => 0.15,
            'procedure' => 0.15,
        ];

        $weights = array_merge($defaults, array_filter($weights, 'is_numeric'));
        $sum = array_sum($weights);

        if ($sum <= 0) {
            return $defaults;
        }

        return array_map(fn ($value) => $value / $sum, $weights);
    }
}
