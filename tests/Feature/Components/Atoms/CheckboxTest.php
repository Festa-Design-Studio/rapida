<?php

it('renders with label', function () {
    $view = $this->blade('<x-atoms.checkbox name="infra_type[]" value="commercial" label="Commercial Infrastructure" />');

    $view->assertSee('Commercial Infrastructure');
    $view->assertSee('type="checkbox"', false);
    $view->assertSee('value="commercial"', false);
});

it('renders with description', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" description="Markets, malls, shops" />');

    $view->assertSee('Markets, malls, shops');
    $view->assertSee('text-body-sm', false);
});

it('renders checked state', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" :checked="true" />');

    $view->assertSee('checked', false);
});

it('renders required state', function () {
    $view = $this->blade('<x-atoms.checkbox name="confirm" label="I confirm" :required="true" />');

    $view->assertSee('required', false);
    $view->assertSee('aria-required="true"', false);
});

it('renders disabled state', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" :disabled="true" />');

    $view->assertSee('disabled', false);
    $view->assertSee('opacity-40', false);
    $view->assertSee('cursor-not-allowed', false);
});

it('uses rapida-blue accent color', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" />');

    $view->assertSee('accent-rapida-blue-700', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" />');

    $view->assertSee('focus:ring-2', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
});

it('entire label row is tappable', function () {
    $view = $this->blade('<x-atoms.checkbox name="test" label="Test" />');

    $view->assertSee('<label', false);
    $view->assertSee('cursor-pointer', false);
});
