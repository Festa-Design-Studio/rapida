<?php

it('renders with text content', function () {
    $view = $this->blade('<x-atoms.badge>Minimal / No Damage</x-atoms.badge>');

    $view->assertSee('Minimal / No Damage');
    $view->assertSee('rounded-full', false);
});

it('renders minimal damage variant', function () {
    $view = $this->blade('<x-atoms.badge variant="minimal">Minimal</x-atoms.badge>');

    $view->assertSee('bg-green-100', false);
    $view->assertSee('text-green-800', false);
    $view->assertSee('border-green-200', false);
});

it('renders partial damage variant with dot', function () {
    $view = $this->blade('<x-atoms.badge variant="partial">Partial</x-atoms.badge>');

    $view->assertSee('bg-amber-100', false);
    $view->assertSee('bg-amber-500', false);
});

it('renders complete damage variant', function () {
    $view = $this->blade('<x-atoms.badge variant="complete">Complete</x-atoms.badge>');

    $view->assertSee('bg-red-100', false);
    $view->assertSee('text-red-800', false);
    $view->assertSee('bg-red-600', false);
});

it('renders synced state', function () {
    $view = $this->blade('<x-atoms.badge variant="synced">Synced</x-atoms.badge>');

    $view->assertSee('bg-green-100', false);
    $view->assertSee('text-green-800', false);
});

it('renders pending state', function () {
    $view = $this->blade('<x-atoms.badge variant="pending">Pending sync</x-atoms.badge>');

    $view->assertSee('bg-amber-100', false);
});

it('renders verified badge', function () {
    $view = $this->blade('<x-atoms.badge variant="verified">Verified Reporter</x-atoms.badge>');

    $view->assertSee('bg-rapida-blue-700', false);
    $view->assertSee('text-white', false);
});

it('renders large size', function () {
    $view = $this->blade('<x-atoms.badge size="lg">Featured</x-atoms.badge>');

    $view->assertSee('px-3', false);
    $view->assertSee('text-body-sm', false);
});

it('dot is aria-hidden', function () {
    $view = $this->blade('<x-atoms.badge variant="minimal">Test</x-atoms.badge>');

    $view->assertSee('aria-hidden="true"', false);
});
