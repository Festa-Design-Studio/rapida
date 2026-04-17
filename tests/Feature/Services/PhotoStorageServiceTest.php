<?php

use App\DataTransferObjects\PhotoResult;
use App\Services\PhotoStorageService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

it('stores a photo downloaded from a URL', function () {
    Storage::fake('public');
    Http::fake([
        'https://example.com/photo.jpg' => Http::response('fake-jpeg-bytes', 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $service = app(PhotoStorageService::class);
    $result = $service->storeFromUrl('https://example.com/photo.jpg');

    expect($result)->toBeInstanceOf(PhotoResult::class)
        ->and($result->url)->toStartWith('photos/')
        ->and($result->url)->toEndWith('.jpg')
        ->and($result->hash)->toBe(hash('sha256', 'fake-jpeg-bytes'))
        ->and($result->sizeBytes)->toBe(strlen('fake-jpeg-bytes'));

    Storage::disk('public')->assertExists($result->url);
});

it('uses basic auth when credentials are provided', function () {
    Storage::fake('public');
    Http::fake([
        'https://api.twilio.com/*' => Http::response('twilio-image-bytes', 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $service = app(PhotoStorageService::class);
    $result = $service->storeFromUrl(
        'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages/SM456/Media/ME789',
        'AC123',
        'auth-token-secret',
    );

    expect($result)->toBeInstanceOf(PhotoResult::class)
        ->and($result->sizeBytes)->toBe(strlen('twilio-image-bytes'));

    Http::assertSent(function ($request) {
        return $request->hasHeader('Authorization')
            && str_starts_with($request->header('Authorization')[0], 'Basic ');
    });
});

it('returns null when the URL download fails', function () {
    Http::fake([
        'https://example.com/broken.jpg' => Http::response('', 500),
    ]);

    $service = app(PhotoStorageService::class);
    $result = $service->storeFromUrl('https://example.com/broken.jpg');

    expect($result)->toBeNull();
});

it('returns null when the URL is unreachable', function () {
    Http::fake([
        'https://unreachable.example.com/*' => function () {
            throw new ConnectionException('Connection refused');
        },
    ]);

    $service = app(PhotoStorageService::class);

    $result = $service->storeFromUrl('https://unreachable.example.com/photo.jpg');

    expect($result)->toBeNull();
});

it('detects png content type correctly', function () {
    Storage::fake('public');
    Http::fake([
        'https://example.com/photo.png' => Http::response('fake-png-bytes', 200, ['Content-Type' => 'image/png']),
    ]);

    $service = app(PhotoStorageService::class);
    $result = $service->storeFromUrl('https://example.com/photo.png');

    expect($result->url)->toEndWith('.png');
});
