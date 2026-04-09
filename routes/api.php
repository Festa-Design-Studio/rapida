<?php

use App\Http\Controllers\Api\ApiAiController;
use App\Http\Controllers\Api\ApiBuildingController;
use App\Http\Controllers\Api\ApiMapPinsController;
use App\Http\Controllers\Api\ApiReportController;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Middleware\VerifyTwilioSignature;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:rapida-global')->group(function () {
    Route::get('/crises/{slug}/buildings', [ApiBuildingController::class, 'footprints'])
        ->middleware('throttle:rapida-pins')
        ->name('api.buildings');

    Route::get('/crises/{slug}/pins', [ApiMapPinsController::class, 'index'])
        ->middleware('throttle:rapida-pins')
        ->name('api.pins');

    Route::post('/reports', [ApiReportController::class, 'store'])
        ->middleware('throttle:rapida-report')
        ->name('api.reports.store');

    Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle'])
        ->middleware([VerifyTwilioSignature::class, 'throttle:rapida-whatsapp'])
        ->name('api.whatsapp.webhook');

    Route::post('/internal/ai-result', [ApiAiController::class, 'receive'])
        ->name('api.ai.result');
});
