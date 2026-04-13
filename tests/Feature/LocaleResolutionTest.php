<?php

use App\Models\Account;
use App\Models\Crisis;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Locale resolution — SetLocaleFromCrisis middleware
|--------------------------------------------------------------------------
|
| These tests lock in the PRD contract: "auto-detect from browser/device
| locale on first load, with a manual toggle always visible." They also
| guard against the regression that motivated the fix — an English speaker
| landing on a French-default crisis and getting a French UI.
|
| The resolution order under test (highest wins):
|   1. session('locale')
|   2. auth('web') Account.preferred_language
|   3. rapida_locale cookie
|   4. Accept-Language header (when supported by the crisis)
|   5. crisis.default_language
|   6. config('app.fallback_locale')
|
*/

function crisisWithLanguages(string $slug, string $default, array $available): Crisis
{
    return Crisis::factory()->create([
        'slug' => $slug,
        'status' => 'active',
        'default_language' => $default,
        'available_languages' => $available,
    ]);
}

it('honours the browser Accept-Language header over the crisis default', function () {
    crisisWithLanguages('accra-test', default: 'fr', available: ['en', 'fr', 'ar']);

    $this->withHeader('Accept-Language', 'en-US,en;q=0.9,fr;q=0.5')
        ->get('/crisis/accra-test')
        ->assertOk();

    expect(app()->getLocale())->toBe('en');
});

it('falls back to the crisis default when the browser prefers an unsupported locale', function () {
    crisisWithLanguages('aleppo-test', default: 'ar', available: ['ar', 'en', 'fr']);

    // Japanese is not in available_languages, so resolver should skip (4)
    // and land on (5) crisis default — Arabic.
    $this->withHeader('Accept-Language', 'ja-JP,ja;q=0.9')
        ->get('/crisis/aleppo-test')
        ->assertOk();

    expect(app()->getLocale())->toBe('ar');
});

it('lets the session toggle override every other signal', function () {
    crisisWithLanguages('accra-session', default: 'fr', available: ['en', 'fr', 'ar']);

    $this->withSession(['locale' => 'ar'])
        ->withHeader('Accept-Language', 'en-US')
        ->get('/crisis/accra-session')
        ->assertOk();

    expect(app()->getLocale())->toBe('ar');
});

it('restores the locale from the rapida_locale cookie when no session exists', function () {
    crisisWithLanguages('accra-cookie', default: 'fr', available: ['en', 'fr', 'ar']);

    $this->withUnencryptedCookie('rapida_locale', 'en')
        ->get('/crisis/accra-cookie')
        ->assertOk();

    expect(app()->getLocale())->toBe('en');
});

it('prefers the authenticated account language over the crisis default', function () {
    $crisis = crisisWithLanguages('accra-account', default: 'fr', available: ['en', 'fr', 'ar']);

    $account = Account::factory()->create([
        'crisis_id' => $crisis->id,
        'preferred_language' => 'ar',
    ]);

    $this->actingAs($account, 'web')
        ->get('/crisis/accra-account')
        ->assertOk();

    expect(app()->getLocale())->toBe('ar');
});

it('renders the global floating language switcher on the crisis page', function () {
    crisisWithLanguages('accra-pill', default: 'en', available: ['en', 'fr', 'ar']);

    $this->get('/crisis/accra-pill')
        ->assertOk()
        ->assertSee('global-language-switcher', escape: false);
});

it('persists the chosen locale to the cookie and session when the switcher posts', function () {
    crisisWithLanguages('accra-post', default: 'en', available: ['en', 'fr', 'ar']);

    $response = $this->from('/crisis/accra-post')
        ->post('/onboarding/language', ['language' => 'fr']);

    $response->assertRedirect('/crisis/accra-post');
    // Cookie is exempt from encryption (see bootstrap/app.php) so the
    // PWA service worker can read the raw value from document.cookie.
    $response->assertCookie('rapida_locale', 'fr', encrypted: false);
    expect(session('locale'))->toBe('fr');
});

it('rejects an unsupported locale submitted to the switcher endpoint', function () {
    crisisWithLanguages('accra-reject', default: 'en', available: ['en', 'fr', 'ar']);

    $this->from('/crisis/accra-reject')
        ->post('/onboarding/language', ['language' => 'klingon'])
        ->assertRedirect();

    expect(session('locale'))->toBe(config('app.fallback_locale'));
});
