<?php

it('renders the submission wizard', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('aria-label="Damage report submission wizard"', false);
    $view->assertSee('Submit what you have');
});

it('composes progress step atom', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('role="progressbar"', false);
    $view->assertSee('Step', false);
});

it('composes photo upload atom in step 1', function () {
    $view = $this->blade('<x-organisms.submission-wizard :currentStep="1" />');

    $view->assertSee('Step 1: Photo Upload', false);
    $view->assertSee('Upload evidence photo');
});

it('composes damage classification molecule in step 3', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('Step 3: Damage Classification', false);
    $view->assertSee('Damage Classification');
});

it('composes infrastructure type and crisis type molecules in step 4', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('Step 4: Infrastructure and Crisis Details', false);
    $view->assertSee('Infrastructure Type');
    $view->assertSee('Crisis Type');
});

it('has navigation buttons with aria labels', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('aria-label="Go to previous step"', false);
    $view->assertSee('aria-label="Go to next step"', false);
    $view->assertSee('aria-label="Submit what you have so far"', false);
});

it('renders review section in step 5', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('Review Your Report');
    $view->assertSee('Submit Report');
});

it('uses form role on the container', function () {
    $view = $this->blade('<x-organisms.submission-wizard />');

    $view->assertSee('role="form"', false);
});
