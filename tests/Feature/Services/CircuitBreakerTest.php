<?php

use App\Services\CircuitBreakerService;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
    $this->breaker = new CircuitBreakerService(
        failureThreshold: 5,
        failureWindowSeconds: 60,
        openDurationSeconds: 30,
    );
    $this->service = 'ai-classifier';
});

it('starts in closed state (available)', function () {
    expect($this->breaker->getState($this->service))->toBe('closed');
    expect($this->breaker->isAvailable($this->service))->toBeTrue();
});

it('records failures and stays available below threshold', function () {
    for ($i = 0; $i < 4; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    expect($this->breaker->isAvailable($this->service))->toBeTrue();
    expect($this->breaker->getState($this->service))->toBe('closed');
});

it('trips to open after 5 failures', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    expect($this->breaker->getState($this->service))->toBe('open');
    expect($this->breaker->isAvailable($this->service))->toBeFalse();
});

it('returns unavailable when open', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    expect($this->breaker->isAvailable($this->service))->toBeFalse();
});

it('transitions to half-open after timeout', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    expect($this->breaker->getState($this->service))->toBe('open');

    // Travel past the open duration
    $this->travel(31)->seconds();

    expect($this->breaker->isAvailable($this->service))->toBeTrue();
    expect($this->breaker->getState($this->service))->toBe('half_open');
});

it('records success in half-open and closes circuit', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    $this->travel(31)->seconds();

    // Trigger half-open transition
    $this->breaker->isAvailable($this->service);

    expect($this->breaker->getState($this->service))->toBe('half_open');

    $this->breaker->recordSuccess($this->service);

    expect($this->breaker->getState($this->service))->toBe('closed');
    expect($this->breaker->isAvailable($this->service))->toBeTrue();
});

it('records failure in half-open and re-opens circuit', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->breaker->recordFailure($this->service);
    }

    $this->travel(31)->seconds();

    // Trigger half-open transition
    $this->breaker->isAvailable($this->service);

    expect($this->breaker->getState($this->service))->toBe('half_open');

    $this->breaker->recordFailure($this->service);

    expect($this->breaker->getState($this->service))->toBe('open');
    expect($this->breaker->isAvailable($this->service))->toBeFalse();
});
