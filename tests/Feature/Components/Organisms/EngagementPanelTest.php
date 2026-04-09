<?php

it('renders the engagement panel', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('aria-label="Community engagement and recognition"', false);
    $view->assertSee('Community Contributions');
});

it('shows community member count', function () {
    $view = $this->blade('<x-organisms.engagement-panel :communityCount="150" />');

    $view->assertSee('150');
    $view->assertSee('Community members have submitted reports');
});

it('shows user report count', function () {
    $view = $this->blade('<x-organisms.engagement-panel :userReportCount="12" />');

    $view->assertSee('12');
    $view->assertSee('Reports you have submitted');
});

it('renders default badges when none provided', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('First Report');
    $view->assertSee('5 Reports');
    $view->assertSee('Verified Reporter');
});

it('composes badge atoms for earned status', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('Earned');
    $view->assertSee('Locked');
});

it('composes icon atoms', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('aria-hidden="true"', false);
});

it('renders submit button', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('Submit Another Report');
});

it('uses region role on container', function () {
    $view = $this->blade('<x-organisms.engagement-panel />');

    $view->assertSee('role="region"', false);
});
