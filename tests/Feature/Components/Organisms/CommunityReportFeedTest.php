<?php

it('renders the community report feed', function () {
    $view = $this->blade('<x-organisms.community-report-feed />');

    $view->assertSee('role="feed"', false);
    $view->assertSee('aria-label="Community damage reports"', false);
});

it('shows empty state when no reports', function () {
    $view = $this->blade('<x-organisms.community-report-feed :reports="[]" />');

    $view->assertSee('No damage reports yet');
    $view->assertSee('Submit a Report');
});

it('shows custom empty message', function () {
    $view = $this->blade('<x-organisms.community-report-feed :reports="[]" emptyMessage="Nothing here yet" />');

    $view->assertSee('Nothing here yet');
});

it('shows skeleton cards when loading', function () {
    $view = $this->blade('<x-organisms.community-report-feed :loading="true" />');

    $view->assertSee('aria-busy="true"', false);
    $view->assertSee('Loading report', false);
});

it('composes damage report card molecules for reports', function () {
    $reports = [
        ['location' => '123 Main St', 'damageLevel' => 'partial', 'syncStatus' => 'synced'],
        ['location' => '456 Oak Ave', 'damageLevel' => 'complete', 'syncStatus' => 'pending'],
    ];

    $view = $this->blade('<x-organisms.community-report-feed :reports="$reports" />', ['reports' => $reports]);

    $view->assertSee('123 Main St');
    $view->assertSee('456 Oak Ave');
});

it('composes offline queue molecule', function () {
    $view = $this->blade('<x-organisms.community-report-feed />');

    $view->assertSee('role="status"', false);
    $view->assertSee('aria-live="polite"', false);
});

it('sets aria-setsize and aria-posinset on report cards', function () {
    $reports = [
        ['location' => 'Place 1'],
        ['location' => 'Place 2'],
    ];

    $view = $this->blade('<x-organisms.community-report-feed :reports="$reports" />', ['reports' => $reports]);

    $view->assertSee('aria-setsize="2"', false);
    $view->assertSee('aria-posinset="1"', false);
    $view->assertSee('aria-posinset="2"', false);
});

it('composes loader atom for skeleton cards', function () {
    $view = $this->blade('<x-organisms.community-report-feed :loading="true" />');

    $view->assertSee('animate-pulse', false);
});
