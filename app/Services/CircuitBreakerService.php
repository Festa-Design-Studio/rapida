<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CircuitBreakerService
{
    private const STATE_CLOSED = 'closed';

    private const STATE_OPEN = 'open';

    private const STATE_HALF_OPEN = 'half_open';

    public function __construct(
        private readonly int $failureThreshold = 5,
        private readonly int $failureWindowSeconds = 60,
        private readonly int $openDurationSeconds = 30,
    ) {}

    public function isAvailable(string $service): bool
    {
        $state = $this->getState($service);

        if ($state === self::STATE_CLOSED) {
            return true;
        }

        if ($state === self::STATE_OPEN) {
            $openedAt = Cache::get("circuit:{$service}:opened_at", 0);

            if (now()->timestamp - $openedAt >= $this->openDurationSeconds) {
                $this->setState($service, self::STATE_HALF_OPEN);

                return true;
            }

            return false;
        }

        // HALF_OPEN: allow one request through to test
        return true;
    }

    public function recordSuccess(string $service): void
    {
        $state = $this->getState($service);

        if ($state === self::STATE_HALF_OPEN) {
            $this->reset($service);
        }
    }

    public function recordFailure(string $service): void
    {
        $state = $this->getState($service);

        if ($state === self::STATE_HALF_OPEN) {
            $this->trip($service);

            return;
        }

        $key = "circuit:{$service}:failures";
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, 1, $this->failureWindowSeconds);
        }

        if ($count >= $this->failureThreshold) {
            $this->trip($service);
        }
    }

    public function getState(string $service): string
    {
        return Cache::get("circuit:{$service}:state", self::STATE_CLOSED);
    }

    private function trip(string $service): void
    {
        $this->setState($service, self::STATE_OPEN);
        Cache::put("circuit:{$service}:opened_at", now()->timestamp, $this->openDurationSeconds + 10);
        Cache::forget("circuit:{$service}:failures");
    }

    private function reset(string $service): void
    {
        $this->setState($service, self::STATE_CLOSED);
        Cache::forget("circuit:{$service}:opened_at");
        Cache::forget("circuit:{$service}:failures");
    }

    private function setState(string $service, string $state): void
    {
        Cache::put("circuit:{$service}:state", $state, 300);
    }
}
