<?php

it('renders with label and slot content', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Location" name="location">
            <input type="text" name="location" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('Location');
    $view->assertSee('name="location"', false);
});

it('shows required indicator', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Name" name="name" :required="true">
            <input type="text" name="name" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('*');
    $view->assertSee('aria-hidden="true"', false);
});

it('shows optional label', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Notes" name="notes" :optional="true">
            <textarea name="notes"></textarea>
        </x-molecules.form-field-group>
    ');

    $view->assertSee('(optional)');
});

it('displays error with role alert', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Email" name="email" error="Email is required">
            <input type="email" name="email" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('Email is required');
    $view->assertSee('role="alert"', false);
    $view->assertSee('text-red-700', false);
});

it('displays help text when no error', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Phone" name="phone" help="Include country code">
            <input type="tel" name="phone" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('Include country code');
    $view->assertSee('text-slate-500', false);
});

it('hides help text when error is present', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Phone" name="phone" help="Include country code" error="Required">
            <input type="tel" name="phone" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('Required');
    $view->assertDontSee('Include country code');
});

it('wraps slot content with flex-col gap layout', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Field" name="field">
            <input type="text" />
        </x-molecules.form-field-group>
    ');

    $view->assertSee('flex flex-col gap-1.5', false);
});

it('renders label with for attribute linking', function () {
    $view = $this->blade('
        <x-molecules.form-field-group label="Description" name="description">
            <textarea></textarea>
        </x-molecules.form-field-group>
    ');

    $view->assertSee('for="description"', false);
});
