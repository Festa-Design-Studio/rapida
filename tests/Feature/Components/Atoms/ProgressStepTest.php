<?php

it('renders dots variant by default', function () {
    $view = $this->blade('<x-atoms.progress-step :current="2" :total="5" />');

    $view->assertSee('role="progressbar"', false);
    $view->assertSee('aria-valuenow="2"', false);
    $view->assertSee('aria-valuemax="5"', false);
});

it('renders correct number of dots', function () {
    $view = $this->blade('<x-atoms.progress-step :current="3" :total="5" />');

    $view->assertSee('bg-teal-700', false);
    $view->assertSee('bg-slate-300', false);
});

it('highlights active step with ring', function () {
    $view = $this->blade('<x-atoms.progress-step :current="2" :total="5" />');

    $view->assertSee('ring-4 ring-teal-100', false);
});

it('renders counter variant', function () {
    $view = $this->blade('<x-atoms.progress-step :current="3" :total="5" variant="counter" />');

    $view->assertSee('Step 3 of 5');
    $view->assertSee('aria-live="polite"', false);
});

it('renders counter with label', function () {
    $view = $this->blade(
        '<x-atoms.progress-step :current="2" :total="5" variant="counter" :labels="$labels" />',
        ['labels' => ['Photo', 'Location', 'Damage', 'Details', 'Confirm']]
    );

    $view->assertSee('Step 2 of 5');
    $view->assertSee('Location');
});

it('renders progress bar variant', function () {
    $view = $this->blade('<x-atoms.progress-step :current="2" :total="5" variant="bar" />');

    $view->assertSee('role="progressbar"', false);
    $view->assertSee('aria-valuenow="40"', false);
    $view->assertSee('width: 40%', false);
});

it('uses smooth transition on progress bar', function () {
    $view = $this->blade('<x-atoms.progress-step :current="1" :total="5" variant="bar" />');

    $view->assertSee('transition-all duration-300', false);
});
