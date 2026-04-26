<?php

use App\Models\Crisis;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| RTL layout — HTTP-level Arabic locale render (gap-42)
|--------------------------------------------------------------------------
|
| Without Pest 4 browser infrastructure (deferred), we still verify at
| the HTTP/HTML level that:
|   - The layout sets <html dir="rtl"> for Arabic.
|   - Reporter-facing routes render successfully under ar locale.
|
| When Pest browser plugin lands, a follow-up test will load the page
| in Playwright + Arabic, take a screenshot, and assert no JS console
| errors — that's the visual-regression gate. This file is the
| server-side gate that has to pass first.
|
*/

it('renders <html dir="rtl"> when the active locale is Arabic', function () {
    Crisis::factory()->create([
        'slug' => 'aleppo-conflict-2026',
        'status' => 'active',
        'default_language' => 'ar',
        'available_languages' => ['ar', 'en', 'fr'],
        'conflict_context' => true,
    ]);

    // SetLocaleFromCrisis middleware resolves locale from session > account
    // > rapida_locale cookie > Accept-Language > crisis default. Use the
    // cookie path for a deterministic test.
    $response = $this->withUnencryptedCookie('rapida_locale', 'ar')
        ->get('/crisis/aleppo-conflict-2026');

    $response->assertSuccessful();
    expect($response->getContent())
        ->toContain('dir="rtl"')
        ->and($response->getContent())->toContain('lang="ar"');
});

it('renders <html dir="ltr"> for non-Arabic locales', function () {
    Crisis::factory()->create([
        'slug' => 'accra-flood-2026',
        'status' => 'active',
        'default_language' => 'en',
    ]);

    app()->setLocale('en');

    $response = $this->get('/crisis/accra-flood-2026');

    $response->assertSuccessful();
    expect($response->getContent())
        ->toContain('dir="ltr"')
        ->and($response->getContent())->toContain('lang="en"');
});
