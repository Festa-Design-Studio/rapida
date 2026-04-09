<?php

namespace App\Jobs;

use App\Models\DamageReport;
use App\Models\RecoveryOutcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class BroadcastRecoveryOutcome implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly RecoveryOutcome $outcome,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $accountIds = DamageReport::where('crisis_id', $this->outcome->crisis_id)
            ->where('h3_cell_id', $this->outcome->h3_cell_id)
            ->whereNotNull('account_id')
            ->distinct()
            ->pluck('account_id');

        Log::info('Broadcasting recovery outcome', [
            'outcome_id' => $this->outcome->id,
            'h3_cell_id' => $this->outcome->h3_cell_id,
            'notified_accounts' => $accountIds->count(),
        ]);
    }
}
