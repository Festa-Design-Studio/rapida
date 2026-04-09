<?php

it('renders fallback svg when icon file does not exist', function () {
    $view = $this->blade('<x-atoms.icon name="nonexistent-icon" />');

    $view->assertSee('<svg', false);
    $view->assertSee('aria-hidden="true"', false);
});

it('applies size classes', function () {
    $view = $this->blade('<x-atoms.icon name="camera" size="lg" />');

    $view->assertSee('w-6 h-6', false);
});

it('defaults to medium size', function () {
    $view = $this->blade('<x-atoms.icon name="camera" />');

    $view->assertSee('w-5 h-5', false);
});

it('renders xs size', function () {
    $view = $this->blade('<x-atoms.icon name="camera" size="xs" />');

    $view->assertSee('w-3 h-3', false);
});

it('renders sm size', function () {
    $view = $this->blade('<x-atoms.icon name="camera" size="sm" />');

    $view->assertSee('w-4 h-4', false);
});

it('renders xl size', function () {
    $view = $this->blade('<x-atoms.icon name="camera" size="xl" />');

    $view->assertSee('w-10 h-10', false);
});

it('renders 2xl size', function () {
    $view = $this->blade('<x-atoms.icon name="camera" size="2xl" />');

    $view->assertSee('w-16 h-16', false);
});

it('is always aria-hidden', function () {
    $view = $this->blade('<x-atoms.icon name="camera" />');

    $view->assertSee('aria-hidden="true"', false);
});

it('renders a real SVG icon from file', function () {
    $view = $this->blade('<x-atoms.icon name="home" />');

    $view->assertSee('viewBox="0 0 24 24"', false);
    $view->assertSee('stroke="currentColor"', false);
    $view->assertSee('<polyline points="9 22 9 12 15 12 15 22"', false);
});

it('wraps icon in an inline-flex span', function () {
    $view = $this->blade('<x-atoms.icon name="search" />');

    $view->assertSee('inline-flex items-center justify-center', false);
});
