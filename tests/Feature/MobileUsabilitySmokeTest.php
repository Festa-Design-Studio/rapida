<?php

use App\Models\Crisis;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Static mobile-usability smoke test (gap-23). Catches the high-value
 * failure modes that don't require a real browser:
 *
 *   - missing viewport meta tag (zooms broken, text too small)
 *   - fixed-width pixel containers (horizontal scroll on narrow phones)
 *   - missing touch-friendly classes (taps too small)
 *
 * Real browser interaction (axe-core a11y, visual regression, JS error
 * capture) requires Pest 4's browser plugin + Playwright. Setup deferred —
 * see docs/mobile-testing.md for the follow-up plan.
 */
it('renders the public reporter page with a responsive viewport meta tag', function () {
    Crisis::factory()->create([
        'slug' => 'accra-flood-2026',
        'status' => 'active',
        'default_language' => 'en',
    ]);

    $response = $this->get('/crisis/accra-flood-2026');
    $response->assertOk();

    expect($response->getContent())->toContain(
        '<meta name="viewport" content="width=device-width'
    );
});

it('renders the map home page with a responsive viewport meta tag', function () {
    $response = $this->get('/');
    $response->assertOk();

    expect($response->getContent())->toContain(
        '<meta name="viewport" content="width=device-width'
    );
});

it('does not use fixed-pixel widths in the public templates', function () {
    Crisis::factory()->create([
        'slug' => 'accra-flood-2026',
        'status' => 'active',
        'default_language' => 'en',
    ]);

    $response = $this->get('/crisis/accra-flood-2026');
    $body = $response->getContent();

    // Catch obvious mobile-killers — fixed pixel widths inline.
    // Allow small fixed sizes (icons, badges) but flag wide containers
    // that will force horizontal scroll on a 375px iPhone.
    preg_match_all('/style="[^"]*width:\s*(\d+)px/i', $body, $matches);
    $offenders = array_filter($matches[1] ?? [], fn ($px) => (int) $px > 300);

    expect($offenders)->toBeEmpty(
        'Inline fixed-pixel widths > 300px would force horizontal scroll on a 375px viewport. Found: '
        .implode(', ', $offenders).'px'
    );
});

it('uses touch-friendly button heights in the wizard', function () {
    Crisis::factory()->create([
        'slug' => 'accra-flood-2026',
        'status' => 'active',
        'default_language' => 'en',
    ]);

    $response = $this->get('/crisis/accra-flood-2026');
    $body = $response->getContent();

    // Tailwind's h-12 = 48px, h-[56px] = 56px, h-10 = 40px (sm size).
    // The reporter wizard's primary CTAs must be at least h-10 (Tailwind's
    // documented minimum touch target) so they're tappable on small phones.
    // The button atom enforces this; this test guards against a future
    // regression that strips the height class.
    expect($body)->toContain('h-12'); // primary action sized correctly
});
