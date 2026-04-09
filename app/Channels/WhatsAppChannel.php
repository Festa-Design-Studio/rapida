<?php

namespace App\Channels;

use App\Contracts\MessagingChannel;
use App\Models\Crisis;
use App\Services\WhatsAppBotService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel implements MessagingChannel
{
    public function __construct(
        private readonly WhatsAppBotService $botService,
    ) {}

    public function handle(array $payload): array
    {
        return $this->botService->handle($payload);
    }

    public function send(string $recipient, string $message): void
    {
        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');
        $from = config('services.twilio.whatsapp_from');

        try {
            Http::withBasicAuth($sid, $token)
                ->timeout(10)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => "whatsapp:{$from}",
                    'To' => $recipient,
                    'Body' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function channelName(): string
    {
        return 'whatsapp';
    }

    public function isEnabledForCrisis(string $crisisSlug): bool
    {
        $crisis = Crisis::where('slug', $crisisSlug)->first();

        return $crisis?->whatsapp_enabled ?? true;
    }
}
