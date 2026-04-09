<?php

namespace App\Services;

use App\Models\DamageReport;

class CompletenessScoreService
{
    public function score(DamageReport $report): int
    {
        $score = 0;
        if ($report->photo_url && $report->photo_url !== 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg') {
            $score += 2;
        }
        if ($report->damage_level && $report->infrastructure_type && $report->crisis_type && $report->debris_required !== null) {
            $score += 3;
        }
        if ($report->infrastructure_name) {
            $score += 1;
        }

        return $score; // Max: 6
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function scoreFromArray(array $data): int
    {
        $score = 0;
        if (! empty($data['photo_url']) || ! empty($data['photo'])) {
            $score += 2;
        }
        if (! empty($data['damage_level']) && ! empty($data['infrastructure_type']) && ! empty($data['crisis_type'])) {
            $score += 3;
        }
        if (! empty($data['infrastructure_name'])) {
            $score += 1;
        }

        return $score;
    }
}
