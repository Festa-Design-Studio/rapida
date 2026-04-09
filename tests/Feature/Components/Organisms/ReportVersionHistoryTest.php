<?php

it('renders the report version history', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('aria-label="Report version history"', false);
    $view->assertSee('Version History');
});

it('renders default version timeline', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('v3');
    $view->assertSee('v2');
    $view->assertSee('v1');
});

it('composes badge atoms for version status', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('Current Version');
    $view->assertSee('Previous');
    $view->assertSee('Original');
});

it('shows version change descriptions', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('Updated damage level from Partial to Complete');
    $view->assertSee('Initial report submitted with photo and location');
});

it('renders restore and view buttons for non-current versions', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('aria-label="Restore version', false);
    $view->assertSee('aria-label="View version', false);
});

it('shows editor name and timestamp', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('Ahmed K.');
    $view->assertSee('Fatima R.');
    $view->assertSee('2026-03-26 14:30');
});

it('uses list role for timeline', function () {
    $view = $this->blade('<x-organisms.report-version-history />');

    $view->assertSee('role="list"', false);
    $view->assertSee('role="listitem"', false);
});

it('accepts custom versions array', function () {
    $versions = [
        [
            'version' => 1,
            'status' => 'current',
            'editedBy' => 'Test User',
            'editedAt' => '2026-01-01 12:00',
            'changes' => 'Custom version text',
        ],
    ];

    $view = $this->blade('<x-organisms.report-version-history :versions="$versions" />', ['versions' => $versions]);

    $view->assertSee('Test User');
    $view->assertSee('Custom version text');
});
