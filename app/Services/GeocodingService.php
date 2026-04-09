<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * @return array{lat: float, lng: float}|null
     */
    public function resolveW3W(string $words): ?array
    {
        $apiKey = config('services.w3w.api_key');
        if (! $apiKey) {
            Log::warning('what3words API key not configured');

            return null;
        }

        try {
            $response = Http::timeout(5)->get('https://api.what3words.com/v3/convert-to-coordinates', [
                'words' => $words,
                'key' => $apiKey,
            ]);

            if ($response->ok() && isset($response['coordinates'])) {
                return [
                    'lat' => $response['coordinates']['lat'],
                    'lng' => $response['coordinates']['lng'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('what3words API error: '.$e->getMessage());
        }

        return null;
    }

    public function isW3WFormat(string $input): bool
    {
        return (bool) preg_match('/^[a-z]+\.[a-z]+\.[a-z]+$/i', trim($input));
    }
}
