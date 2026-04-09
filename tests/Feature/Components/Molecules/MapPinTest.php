<?php

it('renders pin variant by default', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="minimal" />');

    $view->assertSee('bg-green-500', false);
    $view->assertSee('rounded-full', false);
});

it('renders partial damage pin', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="partial" />');

    $view->assertSee('bg-amber-500', false);
});

it('renders complete damage pin', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="complete" />');

    $view->assertSee('bg-red-600', false);
});

it('renders cluster variant with count', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="partial" variant="cluster" :count="12" />');

    $view->assertSee('12');
    $view->assertSee('bg-amber-100', false);
    $view->assertSee('border-amber-300', false);
});

it('has aria-label on pin variant', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="minimal" variant="pin" />');

    $view->assertSee('aria-label="Map pin: Minimal damage"', false);
});

it('has aria-label on cluster variant', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="complete" variant="cluster" :count="5" />');

    $view->assertSee('aria-label="5 reports with Complete destruction"', false);
});

it('uses role img for semantic meaning', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="minimal" />');

    $view->assertSee('role="img"', false);
});

it('cluster meets 48px touch target', function () {
    $view = $this->blade('<x-molecules.map-pin damageLevel="minimal" variant="cluster" :count="3" />');

    $view->assertSee('h-12', false);
    $view->assertSee('w-12', false);
});
