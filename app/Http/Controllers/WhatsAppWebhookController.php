<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private WhatsAppBotService $botService) {}

    public function handle(Request $request): Response
    {
        $result = $this->botService->handle($request->all());

        $twiml = '<?xml version="1.0" encoding="UTF-8"?>';
        $twiml .= '<Response><Message>'.htmlspecialchars($result['message']).'</Message></Response>';

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }
}
