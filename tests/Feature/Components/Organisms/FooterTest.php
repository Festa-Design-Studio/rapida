<?php

it('renders as a contentinfo landmark', function () {
    $view = $this->blade('<x-organisms.footer />');

    $view->assertSee('role="contentinfo"', false);
});

it('shows the UNDP attribution on the left', function () {
    $view = $this->blade('<x-organisms.footer />');

    $view->assertSee('/images/partners/undp-logo.svg');
    $view->assertSee('UNDP Crisis Mapping Tool');
});

it('links to festa.design with safe-link semantics', function () {
    $view = $this->blade('<x-organisms.footer />');

    $view->assertSee('https://festa.design/');
    $view->assertSee('target="_blank"', false);
    $view->assertSee('rel="noopener noreferrer"', false);
});

it('marks the festa logomark as decorative for assistive tech', function () {
    $view = $this->blade('<x-organisms.footer />');

    $view->assertSee('/images/partners/festa-logomark.svg');
    $view->assertSee('aria-hidden="true"', false);
});

it('hides the festa label on mobile and shows on sm+', function () {
    $view = $this->blade('<x-organisms.footer />');

    // Class controls visibility — assertion documents the responsive contract.
    $view->assertSee('hidden sm:inline', false);
    $view->assertSee('Interface designed by Festa.');
});

it('announces the new-tab destination via aria-label', function () {
    $view = $this->blade('<x-organisms.footer />');

    $view->assertSee('opens in a new tab');
});
