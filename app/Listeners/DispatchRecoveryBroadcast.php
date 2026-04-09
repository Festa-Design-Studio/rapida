<?php

namespace App\Listeners;

use App\Events\RecoveryOutcomeCreated;
use App\Jobs\BroadcastRecoveryOutcome;

class DispatchRecoveryBroadcast
{
    public function handle(RecoveryOutcomeCreated $event): void
    {
        BroadcastRecoveryOutcome::dispatch($event->outcome);
    }
}
