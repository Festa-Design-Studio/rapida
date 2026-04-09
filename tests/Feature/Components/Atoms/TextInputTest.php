<?php

it('renders with label and name', function () {
    $view = $this->blade('<x-atoms.text-input name="infra_name" label="Infrastructure name" />');

    $view->assertSee('Infrastructure name');
    $view->assertSee('name="infra_name"', false);
    $view->assertSee('id="infra_name"', false);
});

it('renders default state with correct border', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" />');

    $view->assertSee('border-slate-300', false);
    $view->assertSee('bg-white', false);
});

it('renders error state with red border and message', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" error="This field is required." />');

    $view->assertSee('border-red-600', false);
    $view->assertSee('bg-red-50', false);
    $view->assertSee('aria-invalid="true"', false);
    $view->assertSee('role="alert"', false);
    $view->assertSee('This field is required.');
});

it('renders required field with asterisk and aria', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Name" :required="true" />');

    $view->assertSee('*');
    $view->assertSee('aria-required="true"', false);
    $view->assertSee('required', false);
});

it('renders help text with aria-describedby', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Name" help="Include the building name." />');

    $view->assertSee('Include the building name.');
    $view->assertSee('aria-describedby="test-help"', false);
});

it('renders disabled state', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" :disabled="true" />');

    $view->assertSee('disabled', false);
    $view->assertSee('border-slate-200', false);
    $view->assertSee('bg-slate-50', false);
});

it('renders readonly state', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" :readonly="true" />');

    $view->assertSee('readonly', false);
    $view->assertSee('aria-readonly="true"', false);
});

it('meets 48px minimum touch target', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" />');

    $view->assertSee('h-12', false);
});

it('uses 16px font size for iOS zoom prevention', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" />');

    $view->assertSee('text-body', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.text-input name="test" label="Test" />');

    $view->assertSee('focus:ring-1', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
});
