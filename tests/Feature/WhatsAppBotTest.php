<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('starts a WhatsApp session and receives greeting', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026', 'status' => 'active']);

    $response = $this->postJson('/api/v1/webhooks/whatsapp', [
        'From' => 'whatsapp:+233201234567',
        'Body' => 'RAPIDA accra-flood-2026',
    ]);

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    $response->assertSee('Welcome to RAPIDA', false);
});

it('processes photo step', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026', 'status' => 'active']);

    // Step 0: start
    $this->postJson('/api/v1/webhooks/whatsapp', [
        'From' => 'whatsapp:+233201234567',
        'Body' => 'RAPIDA accra-flood-2026',
    ]);

    // Step 1: send photo
    $response = $this->postJson('/api/v1/webhooks/whatsapp', [
        'From' => 'whatsapp:+233201234567',
        'Body' => '',
        'MediaUrl0' => 'https://example.com/photo.jpg',
    ]);

    $response->assertOk();
    $response->assertSee('Photo received', false);
});

it('completes full WhatsApp flow and creates report', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026', 'status' => 'active']);
    $from = 'whatsapp:+233201234567';

    // Step 0: start
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => 'RAPIDA accra-flood-2026']);
    // Step 1: photo
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => '', 'MediaUrl0' => 'https://example.com/photo.jpg']);
    // Step 2: location
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => '', 'Latitude' => '5.56', 'Longitude' => '-0.20']);
    // Step 3: damage level
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => '2']);
    // Step 4: infrastructure
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => '5']);
    // Step 4b: crisis type
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => '2']);
    // Step 4c: debris
    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => 'no']);
    // Step 5: confirm
    $response = $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => 'confirm']);

    $response->assertOk();
    $response->assertSee('submitted', false);
    expect(DamageReport::count())->toBe(1);
    expect(DamageReport::first()->submitted_via->value)->toBe('whatsapp');
});

it('handles session restart', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026', 'status' => 'active']);
    $from = 'whatsapp:+233201234567';

    $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => 'RAPIDA']);
    $response = $this->postJson('/api/v1/webhooks/whatsapp', ['From' => $from, 'Body' => 'restart']);

    $response->assertOk();
    $response->assertSee('restarted', false);
});
