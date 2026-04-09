<?php

namespace App\Jobs;

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\RecoveryOutcome;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AutoSuggestRecoveryZones implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public readonly Crisis $crisis,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $verifiedH3Cells = DamageReport::where('crisis_id', $this->crisis->id)
            ->whereHas('verification', fn ($q) => $q->where('status', 'verified'))
            ->whereNotNull('h3_cell_id')
            ->distinct()
            ->pluck('h3_cell_id');

        $existingOutcomeCells = RecoveryOutcome::where('crisis_id', $this->crisis->id)
            ->pluck('h3_cell_id');

        $suggestedCells = $verifiedH3Cells->diff($existingOutcomeCells);

        if ($suggestedCells->isNotEmpty()) {
            Log::info('Recovery zones auto-suggested', [
                'crisis_id' => $this->crisis->id,
                'suggested_cells' => $suggestedCells->values()->toArray(),
            ]);
        }
    }
}
