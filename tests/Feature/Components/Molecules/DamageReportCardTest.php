<?php

it('renders with required props', function () {
    $view = $this->blade('<x-molecules.damage-report-card
        photo="/images/damage.jpg"
        damageLevel="partial"
        infrastructureType="Residential"
        location="123 Main St"
        description="Significant wall cracks observed"
        reporterName="Ahmed"
        submittedAt="2026-03-26 10:00"
        syncStatus="synced"
    />');

    $view->assertSee('123 Main St');
    $view->assertSee('Significant wall cracks observed');
    $view->assertSee('Ahmed');
    $view->assertSee('Residential');
});

it('uses article tag for semantic markup', function () {
    $view = $this->blade('<x-molecules.damage-report-card location="Test" />');

    $view->assertSee('<article', false);
    $view->assertSee('</article>', false);
});

it('composes badge atom for damage level', function () {
    $view = $this->blade('<x-molecules.damage-report-card damageLevel="complete" location="Test" />');

    $view->assertSee('Complete');
    $view->assertSee('bg-red-100', false);
});

it('composes badge atom for sync status', function () {
    $view = $this->blade('<x-molecules.damage-report-card syncStatus="pending" location="Test" />');

    $view->assertSee('Pending sync');
    $view->assertSee('bg-amber-100', false);
});

it('has aria-label on article', function () {
    $view = $this->blade('<x-molecules.damage-report-card damageLevel="minimal" location="City Center" />');

    $view->assertSee('aria-label="Damage report: Minimal damage at City Center"', false);
});

it('applies correct card styling', function () {
    $view = $this->blade('<x-molecules.damage-report-card location="Test" />');

    $view->assertSee('bg-white', false);
    $view->assertSee('rounded-xl', false);
    $view->assertSee('border-slate-200', false);
    $view->assertSee('shadow-sm', false);
    $view->assertSee('focus-within:ring-2', false);
});

it('renders compact variant without description', function () {
    $view = $this->blade('<x-molecules.damage-report-card
        variant="compact"
        location="Test"
        description="Should not appear"
    />');

    $view->assertDontSee('Should not appear');
    $view->assertSee('h-28', false);
});

it('renders standard variant with description', function () {
    $view = $this->blade('<x-molecules.damage-report-card
        variant="standard"
        location="Test"
        description="This should appear"
    />');

    $view->assertSee('This should appear');
    $view->assertSee('h-40', false);
});

it('renders photo when provided', function () {
    $view = $this->blade('<x-molecules.damage-report-card photo="/images/photo.jpg" location="Test" />');

    $view->assertSee('src="/images/photo.jpg"', false);
});
