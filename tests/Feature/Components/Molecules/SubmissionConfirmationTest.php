<?php

it('renders confirmation message', function () {
    $view = $this->blade('<x-molecules.submission-confirmation
        reportId="RPT-2026-001"
        submittedAt="2026-03-26 10:00"
        damageLevel="partial"
        syncStatus="synced"
    />');

    $view->assertSee('Report Submitted');
    $view->assertSee('RPT-2026-001');
    $view->assertSee('2026-03-26 10:00');
});

it('composes icon atom for check circle', function () {
    $view = $this->blade('<x-molecules.submission-confirmation />');

    $view->assertSee('text-green-600', false);
});

it('composes badge atom for damage level', function () {
    $view = $this->blade('<x-molecules.submission-confirmation damageLevel="complete" />');

    $view->assertSee('Complete');
    $view->assertSee('bg-red-100', false);
});

it('composes badge atom for sync status', function () {
    $view = $this->blade('<x-molecules.submission-confirmation syncStatus="pending" />');

    $view->assertSee('Pending sync');
    $view->assertSee('bg-amber-100', false);
});

it('composes button atoms for actions', function () {
    $view = $this->blade('<x-molecules.submission-confirmation />');

    $view->assertSee('Submit Another Report');
    $view->assertSee('View Report');
});

it('has ARIA live region', function () {
    $view = $this->blade('<x-molecules.submission-confirmation />');

    $view->assertSee('role="status"', false);
    $view->assertSee('aria-live="polite"', false);
});

it('uses green background for positive confirmation', function () {
    $view = $this->blade('<x-molecules.submission-confirmation />');

    $view->assertSee('bg-green-50', false);
    $view->assertSee('border-green-200', false);
});

it('centers content', function () {
    $view = $this->blade('<x-molecules.submission-confirmation />');

    $view->assertSee('items-center', false);
    $view->assertSee('text-center', false);
});
