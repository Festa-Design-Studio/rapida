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

        if ($report->latitude || $report->w3w_code || $report->landmark_text) {
            $score += 2;
        }

        if ($report->damage_level) {
            $score += 2;
        }

        if ($report->infrastructure_type && $report->crisis_type && $report->debris_required !== null) {
            $score += 1;
        }

        if ($report->infrastructure_name) {
            $score += 1;
        }

        return $score; // V2 max: 8. Credibility threshold: >= 6 (photo + location + damage)
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

        if (! empty($data['latitude']) || ! empty($data['w3w_code']) || ! empty($data['landmark_text'])) {
            $score += 2;
        }

        if (! empty($data['damage_level'])) {
            $score += 2;
        }

        if (! empty($data['infrastructure_type']) && ! empty($data['crisis_type']) && array_key_exists('debris_required', $data) && $data['debris_required'] !== null) {
            $score += 1;
        }

        if (! empty($data['infrastructure_name'])) {
            $score += 1;
        }

        return $score; // V2 max: 8
    }
}
