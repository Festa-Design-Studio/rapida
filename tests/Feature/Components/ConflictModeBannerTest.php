<?php

it('shows banner when show is true', function () {
    $view = $this->blade('<x-molecules.conflict-mode-banner :show="true" />');

    $view->assertSee('role="status"', false);
    $view->assertSee(__('rapida.conflict_mode_banner'));
});

it('hides banner when show is false', function () {
    $view = $this->blade('<x-molecules.conflict-mode-banner :show="false" />');

    $view->assertDontSee(__('rapida.conflict_mode_banner'));
});

it('contains the correct copy text', function () {
    $view = $this->blade('<x-molecules.conflict-mode-banner :show="true" />');

    $view->assertSee('Anonymous mode. We do not store anything that could identify you.');
});
