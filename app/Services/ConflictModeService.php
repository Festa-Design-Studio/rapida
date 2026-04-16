<?php

namespace App\Services;

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;

class ConflictModeService
{
    public function isConflict(Crisis $crisis): bool
    {
        return $crisis->conflict_context ?? false;
    }

    public function applyToSubmission(SubmitReportData $data): void
    {
        if (! $this->isConflict($data->crisis)) {
            return;
        }

        $data->deviceFingerprintId = null;
        $data->reporterTier = 'anonymous';
    }

    public function shouldDisableWhatsApp(Crisis $crisis): bool
    {
        return $this->isConflict($crisis) && ! $crisis->whatsapp_enabled;
    }

    public function shouldDisableFingerprinting(Crisis $crisis): bool
    {
        return $this->isConflict($crisis);
    }

    public function shouldDisableLeaderboard(Crisis $crisis): bool
    {
        return $this->isConflict($crisis);
    }
}
