<?php

it('renders inline spinner by default', function () {
    $view = $this->blade('<x-atoms.loader />');

    $view->assertSee('animate-spin', false);
    $view->assertSee('h-8 w-8', false);
    $view->assertSee('role="status"', false);
});

it('renders small spinner', function () {
    $view = $this->blade('<x-atoms.loader size="sm" />');

    $view->assertSee('h-4 w-4', false);
});

it('renders full screen spinner with message', function () {
    $view = $this->blade('<x-atoms.loader size="lg" message="Loading RAPIDA..." />');

    $view->assertSee('Loading RAPIDA...');
    $view->assertSee('fixed inset-0', false);
    $view->assertSee('z-50', false);
});

it('renders skeleton text', function () {
    $view = $this->blade('<x-atoms.loader variant="skeleton-text" />');

    $view->assertSee('animate-pulse', false);
    $view->assertSee('bg-slate-200', false);
    $view->assertSee('aria-busy="true"', false);
});

it('renders skeleton card', function () {
    $view = $this->blade('<x-atoms.loader variant="skeleton-card" />');

    $view->assertSee('rounded-xl', false);
    $view->assertSee('animate-pulse', false);
    $view->assertSee('aria-busy="true"', false);
});

it('renders skeleton image', function () {
    $view = $this->blade('<x-atoms.loader variant="skeleton-image" />');

    $view->assertSee('aspect-video', false);
    $view->assertSee('bg-slate-200', false);
});

it('renders upload progress ring', function () {
    $view = $this->blade('<x-atoms.loader variant="progress-ring" :percentage="64" />');

    $view->assertSee('64%');
    $view->assertSee('aria-live="polite"', false);
    $view->assertSee('stroke-dasharray', false);
});

it('respects prefers-reduced-motion', function () {
    $view = $this->blade('<x-atoms.loader />');

    $view->assertSee('motion-reduce:animate-pulse', false);
});
