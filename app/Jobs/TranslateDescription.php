<?php

namespace App\Jobs;

use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateDescription implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 15;

    public function __construct(
        public DamageReport $report
    ) {}

    public function handle(): void
    {
        if (! $this->report->description) {
            return;
        }

        if ($this->report->description_original_lang === 'en') {
            $this->report->update(['description_en' => $this->report->description]);

            return;
        }

        $baseUrl = config('services.libretranslate.url');
        $apiKey = config('services.libretranslate.api_key');

        $payload = [
            'q' => $this->report->description,
            'source' => $this->report->description_original_lang ?? 'auto',
            'target' => 'en',
            'format' => 'text',
        ];

        if ($apiKey) {
            $payload['api_key'] = $apiKey;
        }

        $response = Http::timeout(10)
            ->connectTimeout(5)
            ->retry(2, 1000)
            ->post("{$baseUrl}/translate", $payload);

        if ($response->successful() && $response->json('translatedText')) {
            $this->report->update([
                'description_en' => $response->json('translatedText'),
            ]);

            Log::info("Translated description for report {$this->report->id}");
        } else {
            Log::warning("Translation failed for report {$this->report->id}", [
                'status' => $response->status(),
            ]);
        }
    }
}
