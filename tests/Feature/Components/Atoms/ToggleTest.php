<?php

it('renders with label', function () {
    $view = $this->blade('<x-atoms.toggle name="satellite" label="Satellite view" />');

    $view->assertSee('Satellite view');
    $view->assertSee('role="switch"', false);
});

it('renders with description', function () {
    $view = $this->blade('<x-atoms.toggle name="satellite" label="Satellite view" description="Show satellite imagery on the map" />');

    $view->assertSee('Show satellite imagery on the map');
});

it('uses aria-checked for state', function () {
    $view = $this->blade('<x-atoms.toggle name="test" label="Test" />');

    $view->assertSee('aria-checked', false);
    $view->assertSee('aria-labelledby', false);
});

it('has hidden input for form submission', function () {
    $view = $this->blade('<x-atoms.toggle name="satellite" label="Test" />');

    $view->assertSee('type="hidden"', false);
    $view->assertSee('name="satellite"', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.toggle name="test" label="Test" />');

    $view->assertSee('focus:ring-2', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
    $view->assertSee('focus:ring-offset-2', false);
});

it('renders track and thumb', function () {
    $view = $this->blade('<x-atoms.toggle name="test" label="Test" />');

    $view->assertSee('w-11', false);
    $view->assertSee('h-6', false);
    $view->assertSee('h-5 w-5', false);
    $view->assertSee('rounded-full', false);
});

it('supports keyboard space toggle', function () {
    $view = $this->blade('<x-atoms.toggle name="test" label="Test" />');

    $view->assertSee('keydown.space.prevent', false);
});

it('renders disabled state', function () {
    $view = $this->blade('<x-atoms.toggle name="test" label="Test" :disabled="true" />');

    $view->assertSee('opacity-50', false);
    $view->assertSee('disabled', false);
});
