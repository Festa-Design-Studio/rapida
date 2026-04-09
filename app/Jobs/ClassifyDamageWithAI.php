<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassifyDamageWithAI implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(
        public readonly string $photoUrl,
        public readonly string $jobId,
    ) {}

    public function handle(): void
    {
        $aiServiceUrl = config('services.ai.url');
        $secret = config('services.ai.secret');

        if (! $aiServiceUrl) {
            Log::warning('AI service URL not configured — skipping classification');

            return;
        }

        try {
            Http::withHeader('X-Internal-Secret', $secret)
                ->timeout(10)
                ->post("{$aiServiceUrl}/classify", [
                    'photo_url' => $this->photoUrl,
                    'callback_url' => url('/api/v1/internal/ai-result'),
                    'job_id' => $this->jobId,
                ]);
        } catch (\Exception $e) {
            Log::error('AI classification dispatch failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
