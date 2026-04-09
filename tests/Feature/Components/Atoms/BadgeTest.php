<?php

it('renders with text content', function () {
    $view = $this->blade('<x-atoms.badge>Minimal / No Damage</x-atoms.badge>');

    $view->assertSee('Minimal / No Damage');
    $view->assertSee('rounded-full', false);
});

it('renders minimal damage variant', function () {
    $view = $this->blade('<x-atoms.badge variant="minimal">Minimal</x-atoms.badge>');

    $view->assertSee('bg-damage-minimal-ui-surface', false);
    $view->assertSee('text-damage-minimal-ui-text', false);
    $view->assertSee('border-damage-minimal-ui-border', false);
});

it('renders partial damage variant with dot', function () {
    $view = $this->blade('<x-atoms.badge variant="partial">Partial</x-atoms.badge>');

    $view->assertSee('bg-damage-partial-ui-surface', false);
    $view->assertSee('bg-damage-partial-map', false);
});

it('renders complete damage variant', function () {
    $view = $this->blade('<x-atoms.badge variant="complete">Complete</x-atoms.badge>');

    $view->assertSee('bg-damage-complete-ui-surface', false);
    $view->assertSee('text-damage-complete-ui-text', false);
    $view->assertSee('bg-damage-complete-map', false);
});

it('renders synced state', function () {
    $view = $this->blade('<x-atoms.badge variant="synced">Synced</x-atoms.badge>');

    $view->assertSee('bg-ground-green-50', false);
    $view->assertSee('text-ground-green-900', false);
});

it('renders pending state', function () {
    $view = $this->blade('<x-atoms.badge variant="pending">Pending sync</x-atoms.badge>');

    $view->assertSee('bg-alert-amber-50', false);
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
