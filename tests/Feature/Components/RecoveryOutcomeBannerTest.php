<?php

it('renders when conflictContext is false', function () {
    $view = $this->blade(
        '<x-molecules.recovery-outcome-banner outcomeId="outcome-1" areaName="Accra Central" message="Road cleared" :conflictContext="false" />'
    );

    $view->assertSee('role="status"', false);
    $view->assertSee('Road cleared');
});

it('does not render when conflictContext is true', function () {
    $view = $this->blade(
        '<x-molecules.recovery-outcome-banner outcomeId="outcome-1" areaName="Accra Central" message="Road cleared" :conflictContext="true" />'
    );

    $view->assertDontSee('Road cleared');
    $view->assertDontSee('role="status"', false);
});

it('shows area name and message', function () {
    $view = $this->blade(
        '<x-molecules.recovery-outcome-banner outcomeId="outcome-2" areaName="Kumasi West" message="Power restored to the district." :conflictContext="false" />'
    );

    $view->assertSee('Kumasi West');
    $view->assertSee('Power restored to the district.');
    $view->assertSee(__('rapida.recovery_contributed'));
});

it('has dismiss button', function () {
    $view = $this->blade(
        '<x-molecules.recovery-outcome-banner outcomeId="outcome-3" areaName="Test Area" message="Test message" :conflictContext="false" />'
    );

    $view->assertSee('aria-label="Dismiss"', false);
    $view->assertSee('dismiss()', false);
});
