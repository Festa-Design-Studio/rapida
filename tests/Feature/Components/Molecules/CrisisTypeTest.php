<?php

it('renders three crisis type options', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" />');

    $view->assertSee('Natural Disaster');
    $view->assertSee('Technological / Industrial');
    $view->assertSee('Human-made / Conflict');
});

it('includes descriptions for each crisis type', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" />');

    $view->assertSee('Earthquake, flood, hurricane, wildfire, landslide, or other natural event.');
    $view->assertSee('Chemical spill, industrial accident, infrastructure collapse, or power failure.');
    $view->assertSee('Armed conflict, civil unrest, terrorism, or deliberate destruction.');
});

it('uses card variant of radio group atom', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" />');

    $view->assertSee('rounded-xl', false);
    $view->assertSee('border-2', false);
});

it('pre-selects a value', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" value="natural" />');

    $view->assertSee('checked', false);
});

it('renders legend text', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" />');

    $view->assertSee('Crisis Type');
});

it('shows error message', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" error="Please select a crisis type" />');

    $view->assertSee('Please select a crisis type');
    $view->assertSee('role="alert"', false);
});

it('renders radio inputs for selection', function () {
    $view = $this->blade('<x-molecules.crisis-type name="crisis_type" />');

    $view->assertSee('type="radio"', false);
    $view->assertSee('name="crisis_type"', false);
});
