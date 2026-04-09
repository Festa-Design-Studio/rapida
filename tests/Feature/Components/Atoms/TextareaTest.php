<?php

it('renders with label and name', function () {
    $view = $this->blade('<x-atoms.textarea name="description" label="Describe the damage" />');

    $view->assertSee('Describe the damage');
    $view->assertSee('name="description"', false);
    $view->assertSee('id="description"', false);
});

it('renders default state with correct styling', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" />');

    $view->assertSee('border-slate-300', false);
    $view->assertSee('bg-white', false);
    $view->assertSee('resize-none', false);
});

it('renders error state', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" error="Please describe the damage." />');

    $view->assertSee('border-red-600', false);
    $view->assertSee('bg-red-50', false);
    $view->assertSee('aria-invalid="true"', false);
    $view->assertSee('role="alert"', false);
    $view->assertSee('Please describe the damage.');
});

it('renders with character count when maxlength set', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" :maxlength="500" />');

    $view->assertSee('maxlength="500"', false);
    $view->assertSee('aria-live="polite"', false);
    $view->assertSee('0 / 500');
});

it('renders help text', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" help="Optional but helpful." />');

    $view->assertSee('Optional but helpful.');
    $view->assertSee('aria-describedby="test-help"', false);
});

it('sets rows attribute', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" :rows="3" />');

    $view->assertSee('rows="3"', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" />');

    $view->assertSee('focus:ring-1', false);
    $view->assertSee('focus:ring-teal-600', false);
});

it('uses 16px font size', function () {
    $view = $this->blade('<x-atoms.textarea name="test" label="Test" />');

    $view->assertSee('text-body', false);
});
