<?php

it('renders with legend', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="debris" legend="Is there debris?" :options="$opts" />',
        ['opts' => ['yes' => 'Yes', 'no' => 'No']]
    );

    $view->assertSee('Is there debris?');
    $view->assertSee('<fieldset', false);
    $view->assertSee('<legend', false);
});

it('renders standard radio options', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="debris" legend="Debris?" :options="$opts" />',
        ['opts' => ['yes' => 'Yes — debris is present', 'no' => 'No — no visible debris']]
    );

    $view->assertSee('Yes — debris is present');
    $view->assertSee('No — no visible debris');
    $view->assertSee('type="radio"', false);
    $view->assertSee('name="debris"', false);
});

it('renders required indicator', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="test" legend="Test" :required="true" :options="[\'a\' => \'A\']" />'
    );

    $view->assertSee('*');
    $view->assertSee('aria-required="true"', false);
});

it('renders error message', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="test" legend="Test" error="Please select an option." :options="[\'a\' => \'A\']" />'
    );

    $view->assertSee('Please select an option.');
    $view->assertSee('role="alert"', false);
});

it('renders card variant', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="damage" legend="Damage" variant="card" :options="$opts" />',
        ['opts' => ['minimal' => ['label' => 'Minimal', 'description' => 'No structural damage']]]
    );

    $view->assertSee('Minimal');
    $view->assertSee('No structural damage');
    $view->assertSee('rounded-xl', false);
    $view->assertSee('sr-only', false);
});

it('meets 48px minimum touch target for standard variant', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="test" legend="Test" :options="[\'a\' => \'A\']" />'
    );

    $view->assertSee('h-12', false);
});

it('has focus ring on radio inputs', function () {
    $view = $this->blade(
        '<x-atoms.radio-group name="test" legend="Test" :options="[\'a\' => \'A\']" />'
    );

    $view->assertSee('focus:ring-2', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
});
