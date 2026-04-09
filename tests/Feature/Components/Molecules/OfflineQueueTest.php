<?php

it('renders online status', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="0" status="online" />');

    $view->assertSee('Online');
    $view->assertSee('bg-green-100', false);
});

it('renders offline status with icon', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="3" status="offline" />');

    $view->assertSee('Offline');
    $view->assertSee('bg-red-100', false);
});

it('renders syncing status with loader', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="2" status="syncing" />');

    $view->assertSee('Syncing...');
    $view->assertSee('animate-spin', false);
});

it('shows pending count badge when count is greater than zero', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="5" status="offline" />');

    $view->assertSee('5 pending');
    $view->assertSee('bg-amber-100', false);
});

it('hides pending badge when count is zero', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="0" status="online" />');

    $view->assertDontSee('0 pending');
});

it('has ARIA live region for status updates', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="0" status="online" />');

    $view->assertSee('role="status"', false);
    $view->assertSee('aria-live="polite"', false);
});

it('uses flex items-center layout', function () {
    $view = $this->blade('<x-molecules.offline-queue :pendingCount="0" status="online" />');

    $view->assertSee('flex items-center gap-2', false);
});
