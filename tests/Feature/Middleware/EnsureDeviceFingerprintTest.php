<?php

use App\Http\Middleware\EnsureDeviceFingerprint;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Cookie;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Anonymous re-find loop — EnsureDeviceFingerprint middleware
|--------------------------------------------------------------------------
|
| The contract: an anonymous reporter who submits a report can return to
| /my-reports on the same device and see it again, without ever creating
| an account. This was broken — the route read a `rapida_device_fingerprint`
| cookie that nothing in the codebase ever wrote.
|
| These tests guard:
|   - cookie issued on first hit to a public route
|   - cookie is idempotent (existing one preserved)
|   - conflict-context crises do NOT receive the cookie (privacy)
|   - the end-to-end loop: submit-with-cookie → re-find-with-cookie
|
*/

/**
 * Pull the issued device-fingerprint cookie off a response (filters out
 * Laravel's XSRF / session cookies). Returns null if it wasn't issued.
 */
function fingerprintCookie($response): ?Cookie
{
    foreach ($response->headers->getCookies() as $cookie) {
        if ($cookie->getName() === EnsureDeviceFingerprint::COOKIE) {
            return $cookie;
        }
    }

    return null;
}

it('issues the device fingerprint cookie on first visit', function () {
    Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);

    $response = $this->get('/my-reports');

    $response->assertSuccessful();
    $cookie = fingerprintCookie($response);
    expect($cookie)->not->toBeNull()
        ->and($cookie->getValue())->not->toBeEmpty()
        ->and($cookie->isHttpOnly())->toBeTrue();
});

it('preserves an existing cookie instead of issuing a new one', function () {
    Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);

    $response = $this->withCookie(EnsureDeviceFingerprint::COOKIE, 'existing-uuid-1234')
        ->get('/my-reports');

    $response->assertSuccessful();
    expect(fingerprintCookie($response))->toBeNull();
});

it('does NOT issue the cookie on a conflict-context crisis', function () {
    Crisis::factory()->create(['status' => 'active', 'conflict_context' => true]);

    $response = $this->get('/my-reports');

    $response->assertSuccessful();
    expect(fingerprintCookie($response))->toBeNull();
});

it('returns the reporters reports when the cookie matches', function () {
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);
    $fingerprint = 'cookie-uuid-abc';

    $mine = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'device_fingerprint_id' => $fingerprint,
        'submitted_at' => now(),
    ]);

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'device_fingerprint_id' => 'someone-else',
        'submitted_at' => now(),
    ]);

    $response = $this->withCookie(EnsureDeviceFingerprint::COOKIE, $fingerprint)
        ->get('/my-reports');

    $response->assertSuccessful();
    expect($response->viewData('reports')->pluck('id')->all())
        ->toBe([$mine->id]);
});

it('returns the empty state when no cookie is present yet', function () {
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'device_fingerprint_id' => 'some-other-device',
    ]);

    $response = $this->get('/my-reports');

    $response->assertSuccessful();
    expect($response->viewData('reports'))->toBeEmpty();
});
