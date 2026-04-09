<?php

it('renders with coordinates and damage level', function () {
    $view = $this->blade('<x-organisms.map-report latitude="5.556" longitude="-0.197" damageLevel="partial" label="Makola Market" />');

    $view->assertSee('rapidaReportMap', false);
    $view->assertSee('5.556', false);
    $view->assertSee('-0.197', false);
    $view->assertSee('partial', false);
    $view->assertSee('Makola Market', false);
});

it('shows fallback when no coordinates', function () {
    $view = $this->blade('<x-organisms.map-report latitude="" longitude="" />');

    $view->assertSee('Location not available');
});

it('uses region role', function () {
    $view = $this->blade('<x-organisms.map-report latitude="5.5" longitude="-0.2" />');

    $view->assertSee('role="region"', false);
    $view->assertSee('aria-label="Report location map"', false);
});

it('accepts custom height', function () {
    $view = $this->blade('<x-organisms.map-report latitude="5.5" longitude="-0.2" height="h-64" />');

    $view->assertSee('h-64', false);
});

it('defaults to zoom 17 for street-level view', function () {
    $view = $this->blade('<x-organisms.map-report latitude="5.5" longitude="-0.2" />');

    $view->assertSee('zoom: 17', false);
});
