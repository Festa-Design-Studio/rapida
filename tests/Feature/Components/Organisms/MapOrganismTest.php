<?php

it('renders the map organism', function () {
    $view = $this->blade('<x-organisms.map-organism />');

    $view->assertSee('aria-label="Damage report map"', false);
    $view->assertSee('id="rapida-map"', false);
});

it('shows fallback when no active crisis', function () {
    $view = $this->blade('<x-organisms.map-organism />');

    $view->assertSee('No active crisis');
});

it('renders real map when crisis slug provided', function () {
    $view = $this->blade('<x-organisms.map-organism crisisSlug="accra-flood-2026" />');

    $view->assertSee('rapidaMap', false);
    $view->assertSee('/api/v1/crises/accra-flood-2026/buildings', false);
    $view->assertSee('/api/v1/crises/accra-flood-2026/pins', false);
});

it('renders damage level legend', function () {
    $view = $this->blade('<x-organisms.map-organism />');

    $view->assertSee('Damage Level');
    $view->assertSee('Minimal');
    $view->assertSee('Partial');
    $view->assertSee('Complete');
});

it('uses region role on container', function () {
    $view = $this->blade('<x-organisms.map-organism />');

    $view->assertSee('role="region"', false);
});

it('accepts custom height prop', function () {
    $view = $this->blade('<x-organisms.map-organism height="h-[800px]" />');

    $view->assertSee('h-[800px]', false);
});

it('accepts fullscreen prop', function () {
    $view = $this->blade('<x-organisms.map-organism :fullscreen="true" />');

    $view->assertDontSee('border-slate-200', false);
    $view->assertDontSee('rounded-xl', false);
});
