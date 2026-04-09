<?php

namespace App\Jobs;

use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CalculateTrustedDevice implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        public readonly DamageReport $report,
    ) {}

    public function handle(): void
    {
        $fingerprint = $this->report->device_fingerprint_id;

        if (! $fingerprint) {
            return;
        }

        $account = $this->report->account;

        if (! $account || $account->is_trusted_device) {
            return;
        }

        $totalReports = DamageReport::where('device_fingerprint_id', $fingerprint)->count();
        $flaggedReports = DamageReport::where('device_fingerprint_id', $fingerprint)
            ->where('is_flagged', true)
            ->count();

        if ($totalReports >= 5 && $flaggedReports <= 1) {
            $account->update([
                'is_trusted_device' => true,
                'trusted_since' => now(),
            ]);
        }
    }
}
