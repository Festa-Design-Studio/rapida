<?php

namespace App\Jobs;

use App\Models\Crisis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ArchiveCrisisData implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct()
    {
        $this->onQueue('exports');
    }

    public function handle(): void
    {
        $expiredCrises = Crisis::where('status', 'active')
            ->whereRaw("created_at + (data_retention_days || ' days')::interval < now()")
            ->get();

        foreach ($expiredCrises as $crisis) {
            $crisis->update(['status' => 'archived']);

            Log::info('Crisis archived due to data retention expiry', [
                'crisis_id' => $crisis->id,
                'slug' => $crisis->slug,
                'retention_days' => $crisis->data_retention_days,
            ]);
        }
    }
}
