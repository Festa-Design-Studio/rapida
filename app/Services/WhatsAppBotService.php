<?php

namespace App\Services;

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WhatsAppBotService
{
    private const SESSION_TTL = 1800; // 30 minutes

    private const MAX_RETRIES = 3;

    public function __construct(
        private readonly CompletenessScoreService $scoreService,
        private readonly ReportSubmissionService $submissionService,
        private readonly ConflictModeService $conflictModeService,
    ) {}

    /**
     * @param  array{From?: string, Body?: string, MediaUrl0?: string, Latitude?: string, Longitude?: string}  $payload
     * @return array{message: string}
     */
    public function handle(array $payload): array
    {
        $phone = $payload['From'] ?? '';
        $body = trim($payload['Body'] ?? '');
        $mediaUrl = $payload['MediaUrl0'] ?? null;
        $latitude = $payload['Latitude'] ?? null;
        $longitude = $payload['Longitude'] ?? null;

        $sessionKey = $this->sessionKey($phone);
        $session = $this->getSession($sessionKey);
        $lang = $session['language'] ?? $this->detectLanguage($body);

        // Global interrupt commands
        if ($this->isRestart($body)) {
            Cache::forget($sessionKey);

            return ['message' => __('whatsapp.session_restarted', [], $lang)];
        }
        if ($this->isCancel($body)) {
            Cache::forget($sessionKey);

            return ['message' => __('whatsapp.session_cancelled', [], $lang)];
        }

        $step = $session['step'] ?? 0;

        return match ((string) $step) {
            '0' => $this->stepStart($sessionKey, $body, $lang),
            '1' => $this->stepPhoto($sessionKey, $session, $mediaUrl, $lang),
            '2' => $this->stepLocation($sessionKey, $session, $latitude, $longitude, $body, $lang),
            '3' => $this->stepDamage($sessionKey, $session, $body, $lang),
            '4' => $this->stepInfra($sessionKey, $session, $body, $lang),
            '4b' => $this->stepCrisisType($sessionKey, $session, $body, $lang),
            '4c' => $this->stepDebris($sessionKey, $session, $body, $lang),
            '5' => $this->stepConfirm($sessionKey, $session, $body, $lang),
            default => ['message' => __('whatsapp.unknown_state', [], $lang)],
        };
    }

    /**
     * @return array{message: string}
     */
    private function stepStart(string $key, string $body, string $lang): array
    {
        $crisisSlug = $this->extractCrisisSlug($body);
        $crisis = Crisis::where('slug', $crisisSlug)->first();

        if ($crisis && ! $crisis->whatsapp_enabled) {
            return ['message' => __('whatsapp.whatsapp_disabled', [], $lang)];
        }

        $session = [
            'step' => 1,
            'language' => $lang,
            'crisis_slug' => $crisisSlug,
            'conflict_context' => $crisis ? $this->conflictModeService->isConflict($crisis) : false,
            'idempotency_key' => Str::uuid()->toString(),
            'retry_count' => 0,
        ];
        $this->setSession($key, $session);

        return ['message' => __('whatsapp.welcome_send_photo', [], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepPhoto(string $key, array $session, ?string $mediaUrl, string $lang): array
    {
        if (! $mediaUrl) {
            $session['retry_count'] = ($session['retry_count'] ?? 0) + 1;
            if ($session['retry_count'] >= self::MAX_RETRIES) {
                Cache::forget($key);

                return ['message' => __('whatsapp.max_retries_exceeded', [], $lang)];
            }
            $this->setSession($key, $session);

            return ['message' => __('whatsapp.please_send_photo', [], $lang)];
        }

        $session['step'] = 2;
        $session['photo_url'] = $mediaUrl;
        $session['photo_hash'] = hash('sha256', $mediaUrl);
        $session['retry_count'] = 0;
        $this->setSession($key, $session);

        $locationPrompt = ($session['conflict_context'] ?? false)
            ? __('whatsapp.location_conflict_mode', [], $lang)
            : __('whatsapp.photo_received_send_location', [], $lang);

        return ['message' => $locationPrompt];
    }

    /**
     * @return array{message: string}
     */
    private function stepLocation(string $key, array $session, ?string $lat, ?string $lon, string $body, string $lang): array
    {
        if ($lat && $lon) {
            $session['latitude'] = (float) $lat;
            $session['longitude'] = (float) $lon;
            $session['location_method'] = 'whatsapp_pin';
        } elseif ($this->isW3W($body)) {
            $session['w3w_code'] = $body;
            $session['location_method'] = 'w3w';
        } else {
            $session['landmark_text'] = $body;
            $session['location_method'] = 'landmark_text';
        }

        $session['step'] = 3;
        $this->setSession($key, $session);

        $suggestion = $session['ai_suggested_level'] ?? null;

        return ['message' => __('whatsapp.damage_options', ['suggestion' => $suggestion ?? 'pending'], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepDamage(string $key, array $session, string $body, string $lang): array
    {
        $level = match (trim($body)) {
            '1', 'minimal' => 'minimal',
            '2', 'partial' => 'partial',
            '3', 'complete' => 'complete',
            default => null,
        };

        if (! $level) {
            return ['message' => __('whatsapp.damage_options', ['suggestion' => ''], $lang)];
        }

        $session['step'] = 4;
        $session['damage_level'] = $level;
        $this->setSession($key, $session);

        return ['message' => __('whatsapp.infra_options', [], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepInfra(string $key, array $session, string $body, string $lang): array
    {
        $types = ['commercial', 'government', 'utility', 'transport', 'community', 'public_recreation', 'other'];
        $index = ((int) trim($body)) - 1;
        $infraType = $types[$index] ?? 'other';

        $session['step'] = '4b';
        $session['infrastructure_type'] = $infraType;
        $this->setSession($key, $session);

        return ['message' => __('whatsapp.crisis_type_options', [], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepCrisisType(string $key, array $session, string $body, string $lang): array
    {
        $types = ['earthquake', 'flood', 'tsunami', 'hurricane', 'wildfire', 'explosion', 'conflict'];
        $index = ((int) trim($body)) - 1;
        $crisisType = $types[$index] ?? 'flood';

        $session['step'] = '4c';
        $session['crisis_type'] = $crisisType;
        $this->setSession($key, $session);

        return ['message' => __('whatsapp.debris_question', [], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepDebris(string $key, array $session, string $body, string $lang): array
    {
        $answer = strtolower(trim($body));
        $session['debris_required'] = in_array($answer, ['yes', 'oui', 'si', "\u{662F}", "\u{434}\u{430}", "\u{646}\u{639}\u{645}", '1']);

        $session['step'] = 5;
        $this->setSession($key, $session);

        $location = $session['landmark_text']
            ?? $session['w3w_code']
            ?? ($session['latitude'] ?? '?').', '.($session['longitude'] ?? '?');

        return ['message' => __('whatsapp.confirm_summary', [
            'location' => $location,
            'damage' => $session['damage_level'],
            'infra_type' => $session['infrastructure_type'],
        ], $lang)];
    }

    /**
     * @return array{message: string}
     */
    private function stepConfirm(string $key, array $session, string $body, string $lang): array
    {
        if (! $this->isConfirmation($body)) {
            return ['message' => __('whatsapp.confirm_or_restart', [], $lang)];
        }

        $crisis = Crisis::where('slug', $session['crisis_slug'])->first();
        if (! $crisis) {
            $crisis = Crisis::where('status', 'active')->first();
        }

        $data = new SubmitReportData(
            crisis: $crisis,
            latitude: isset($session['latitude']) ? (float) $session['latitude'] : null,
            longitude: isset($session['longitude']) ? (float) $session['longitude'] : null,
            w3wCode: $session['w3w_code'] ?? null,
            landmarkText: $session['landmark_text'] ?? null,
            damageLevel: $session['damage_level'] ?? 'partial',
            infrastructureType: $session['infrastructure_type'] ?? 'other',
            crisisType: $session['crisis_type'] ?? 'flood',
            debrisRequired: $session['debris_required'] ?? false,
            deviceFingerprintId: $session['device_fingerprint_id'] ?? null,
            idempotencyKey: $session['idempotency_key'] ?? null,
            buildingFootprintId: $session['building_footprint_id'] ?? null,
            locationMethod: $session['location_method'] ?? 'coordinate_only',
            submittedVia: 'whatsapp',
        );

        $report = $this->submissionService->submit($data);

        Cache::forget($key);

        return ['message' => __('whatsapp.report_submitted', [
            'report_id' => substr($report->id, 0, 8),
        ], $lang)];
    }

    private function sessionKey(string $phone): string
    {
        return 'wa_session:'.hash('sha256', $phone);
    }

    /**
     * @return array<string, mixed>
     */
    private function getSession(string $key): array
    {
        return Cache::get($key, []);
    }

    /**
     * @param  array<string, mixed>  $session
     */
    private function setSession(string $key, array $session): void
    {
        Cache::put($key, $session, self::SESSION_TTL);
    }

    private function detectLanguage(string $body): string
    {
        $body = strtolower(trim($body));

        return match (true) {
            str_starts_with($body, 'fr'), in_array($body, ['bonjour', 'oui']) => 'fr',
            str_starts_with($body, 'ar'), (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $body) => 'ar',
            str_starts_with($body, 'es'), in_array($body, ['hola', 'si']) => 'es',
            str_starts_with($body, 'zh'), (bool) preg_match('/[\x{4E00}-\x{9FFF}]/u', $body) => 'zh',
            str_starts_with($body, 'ru'), (bool) preg_match('/[\x{0400}-\x{04FF}]/u', $body) => 'ru',
            default => 'en',
        };
    }

    private function extractCrisisSlug(string $body): string
    {
        // "RAPIDA accra-flood-2026" -> "accra-flood-2026"
        $parts = explode(' ', trim($body));
        if (count($parts) >= 2) {
            $slug = end($parts);
            if (Crisis::where('slug', $slug)->exists()) {
                return $slug;
            }
        }

        // Fallback to first active crisis
        return Crisis::where('status', 'active')->value('slug') ?? 'default';
    }

    private function isW3W(string $body): bool
    {
        return (bool) preg_match('/^[a-z]+\.[a-z]+\.[a-z]+$/i', trim($body));
    }

    private function isConfirmation(string $body): bool
    {
        return in_array(strtolower(trim($body)), ['confirm', 'yes', 'oui', 'si', "\u{662F}", 'da', "\u{646}\u{639}\u{645}", '1', 'confirmer']);
    }

    private function isRestart(string $body): bool
    {
        return in_array(strtolower(trim($body)), ['restart', 'recommencer', 'reiniciar', "\u{91CD}\u{65B0}\u{5F00}\u{59CB}"]);
    }

    private function isCancel(string $body): bool
    {
        return in_array(strtolower(trim($body)), ['stop', 'cancel', 'annuler', 'cancelar', "\u{53D6}\u{6D88}"]);
    }
}
