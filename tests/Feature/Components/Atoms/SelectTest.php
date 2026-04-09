<?php

it('renders with label and name', function () {
    $view = $this->blade('<x-atoms.select name="sort" label="Sort reports by" />');

    $view->assertSee('Sort reports by');
    $view->assertSee('name="sort"', false);
    $view->assertSee('id="sort"', false);
});

it('renders placeholder as disabled option', function () {
    $view = $this->blade('<x-atoms.select name="sort" label="Sort" placeholder="Select sort order..." />');

    $view->assertSee('Select sort order...');
    $view->assertSee('disabled', false);
});

it('renders options from array', function () {
    $view = $this->blade(
        '<x-atoms.select name="sort" label="Sort" :options="$options" />',
        ['options' => ['newest' => 'Newest first', 'oldest' => 'Oldest first']]
    );

    $view->assertSee('Newest first');
    $view->assertSee('Oldest first');
    $view->assertSee('value="newest"', false);
});

it('renders default state with correct styling', function () {
    $view = $this->blade('<x-atoms.select name="test" label="Test" />');

    $view->assertSee('border-slate-300', false);
    $view->assertSee('bg-white', false);
    $view->assertSee('appearance-none', false);
});

it('renders error state', function () {
    $view = $this->blade('<x-atoms.select name="test" label="Test" error="Please select an option." />');

    $view->assertSee('border-red-600', false);
    $view->assertSee('bg-red-50', false);
    $view->assertSee('aria-invalid="true"', false);
    $view->assertSee('Please select an option.');
});

it('renders chevron icon', function () {
    $view = $this->blade('<x-atoms.select name="test" label="Test" />');

    $view->assertSee('pointer-events-none', false);
    $view->assertSee('<svg', false);
});

it('meets 48px minimum touch target', function () {
    $view = $this->blade('<x-atoms.select name="test" label="Test" />');

    $view->assertSee('h-12', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.select name="test" label="Test" />');

    $view->assertSee('focus:ring-1', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
});
