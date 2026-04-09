<?php

namespace App\Events;

use App\Models\RecoveryOutcome;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecoveryOutcomeCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly RecoveryOutcome $outcome,
    ) {}
}
