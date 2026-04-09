<?php

it('renders the navigation header', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('role="banner"', false);
    $view->assertSee('RAPIDA');
});

it('renders safe exit button', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('Safe Exit');
    $view->assertSee('aria-label="Safe exit', false);
});

it('composes language switcher molecule', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('aria-label="Language selection"', false);
});

it('renders main navigation links', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('Report');
    $view->assertSee('Map');
    $view->assertSee('My Reports');
});

it('shows active state for current route', function () {
    $view = $this->blade('<x-organisms.navigation-header currentRoute="report" />');

    $view->assertSee('aria-current="page"', false);
    $view->assertSee('bg-teal-100', false);
});

it('renders online status badge', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('Online');
});

it('uses sticky positioning', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('sticky', false);
    $view->assertSee('top-0', false);
    $view->assertSee('z-40', false);
});

it('has accessible home link', function () {
    $view = $this->blade('<x-organisms.navigation-header />');

    $view->assertSee('aria-label="RAPIDA home"', false);
});
