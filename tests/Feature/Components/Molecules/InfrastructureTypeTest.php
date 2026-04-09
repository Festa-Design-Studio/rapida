<?php

it('renders all eight infrastructure types', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" />');

    $view->assertSee('Residential');
    $view->assertSee('Commercial');
    $view->assertSee('Hospital / Health');
    $view->assertSee('School / Education');
    $view->assertSee('Road / Bridge');
    $view->assertSee('Utility / Power');
    $view->assertSee('Government');
    $view->assertSee('Community / Religious');
});

it('renders descriptions for each type', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" />');

    $view->assertSee('Houses, apartments, shelters');
    $view->assertSee('Clinics, hospitals, pharmacies');
});

it('composes checkbox atoms', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" />');

    $view->assertSee('type="checkbox"', false);
});

it('pre-checks values from array', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" :values="[\'residential\', \'hospital\']" />');

    $view->assertSee('checked', false);
});

it('renders fieldset with legend', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" />');

    $view->assertSee('<fieldset', false);
    $view->assertSee('Infrastructure Type');
});

it('shows required indicator', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" :required="true" />');

    $view->assertSee('aria-required="true"', false);
});

it('shows error message with alert role', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" error="Select at least one type" />');

    $view->assertSee('Select at least one type');
    $view->assertSee('role="alert"', false);
});

it('uses grid layout for options', function () {
    $view = $this->blade('<x-molecules.infrastructure-type name="infrastructure_type" />');

    $view->assertSee('grid', false);
    $view->assertSee('sm:grid-cols-2', false);
});
