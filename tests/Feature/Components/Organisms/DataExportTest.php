<?php

it('renders the data export interface', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('aria-label="Data export interface"', false);
    $view->assertSee('Export Data');
});

it('composes select atom for format selection', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('CSV (Spreadsheet)');
    $view->assertSee('GeoJSON (Map Data)');
    $view->assertSee('PDF (Report)');
});

it('renders date range filters', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('From Date');
    $view->assertSee('To Date');
    $view->assertSee('type="date"', false);
});

it('composes checkbox atoms for field selection', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('Location / Address');
    $view->assertSee('Damage Level');
    $view->assertSee('Infrastructure Type');
    $view->assertSee('GPS Coordinates');
});

it('has export button with aria label', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('aria-label="Start data export"', false);
    $view->assertSee('Export Data');
});

it('composes form field group molecules', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('Export Format');
    $view->assertSee('Damage Level Filter');
});

it('renders damage level filter', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('All Levels');
});

it('uses region role on container', function () {
    $view = $this->blade('<x-organisms.data-export />');

    $view->assertSee('role="region"', false);
});
