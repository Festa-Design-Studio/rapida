<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PauseModeService
{
    public function isPaused(string $crisisSlug): bool
    {
        return Cache::get("crisis:{$crisisSlug}:paused", false);
    }

    public function pause(string $crisisSlug): void
    {
        Cache::put("crisis:{$crisisSlug}:paused", true);
    }

    public function resume(string $crisisSlug): void
    {
        Cache::forget("crisis:{$crisisSlug}:paused");
    }
}
