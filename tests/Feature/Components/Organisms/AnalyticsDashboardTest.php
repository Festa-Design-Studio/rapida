<?php

it('renders the analytics dashboard', function () {
    $view = $this->blade('<x-organisms.analytics-dashboard />');

    $view->assertSee('aria-label="UNDP Analytics Dashboard"', false);
    $view->assertSee('Analytics Dashboard');
});

it('renders KPI cards', function () {
    $stats = [
        'totalReports' => 42,
        'byDamageLevel' => ['minimal' => 20, 'partial' => 15, 'complete' => 7],
        'byCrisisType' => ['natural' => 30, 'technological' => 8, 'human-made' => 4],
        'recentReports' => [],
        'syncedCount' => 38,
        'pendingCount' => 4,
    ];

    $view = $this->blade('<x-organisms.analytics-dashboard :stats="$stats" />', ['stats' => $stats]);

    $view->assertSee('42');
    $view->assertSee('20');
    $view->assertSee('15');
    $view->assertSee('7');
});

it('composes badge atoms for damage levels', function () {
    $view = $this->blade('<x-organisms.analytics-dashboard />');

    $view->assertSee('Minimal');
    $view->assertSee('Partial');
    $view->assertSee('Complete');
});

it('shows crisis type breakdown', function () {
    $stats = [
        'byCrisisType' => ['natural' => 10, 'technological' => 5, 'human-made' => 3],
    ];

    $view = $this->blade('<x-organisms.analytics-dashboard :stats="$stats" />', ['stats' => $stats]);

    $view->assertSee('Natural');
    $view->assertSee('Technological');
    $view->assertSee('Human-made');
});

it('composes notification molecule', function () {
    $view = $this->blade('<x-organisms.analytics-dashboard />');

    $view->assertSee('Data refreshes every 30 seconds.');
});

it('shows sync status section', function () {
    $stats = ['syncedCount' => 25, 'pendingCount' => 3];

    $view = $this->blade('<x-organisms.analytics-dashboard :stats="$stats" />', ['stats' => $stats]);

    $view->assertSee('25 Synced');
    $view->assertSee('3 Pending');
});

it('uses region role on container', function () {
    $view = $this->blade('<x-organisms.analytics-dashboard />');

    $view->assertSee('role="region"', false);
});

it('renders recent reports when provided', function () {
    $stats = [
        'recentReports' => [
            ['location' => 'Test Location', 'damageLevel' => 'partial'],
        ],
    ];

    $view = $this->blade('<x-organisms.analytics-dashboard :stats="$stats" />', ['stats' => $stats]);

    $view->assertSee('Test Location');
});
