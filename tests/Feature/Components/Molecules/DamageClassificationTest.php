<?php

it('renders three damage level options', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" />');

    $view->assertSee('Minimal / No Damage');
    $view->assertSee('Partial Damage');
    $view->assertSee('Complete Destruction');
});

it('includes descriptions for each level', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" />');

    $view->assertSee('Superficial damage; building is safe and functional.');
    $view->assertSee('Structural issues; building may be unsafe to occupy.');
    $view->assertSee('Total structural failure; building is uninhabitable.');
});

it('renders color dots for each option', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" />');

    $view->assertSee('bg-damage-minimal-map', false);
    $view->assertSee('bg-damage-partial-map', false);
    $view->assertSee('bg-damage-complete-map', false);
});

it('uses card variant of radio group atom', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" />');

    $view->assertSee('rounded-xl', false);
    $view->assertSee('border-2', false);
});

it('pre-selects a value', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" value="partial" />');

    $view->assertSee('checked', false);
});

it('renders legend text', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" />');

    $view->assertSee('Damage Classification');
});

it('shows error message', function () {
    $view = $this->blade('<x-molecules.damage-classification name="damage_level" error="Please select a damage level" />');

    $view->assertSee('Please select a damage level');
    $view->assertSee('role="alert"', false);
});
