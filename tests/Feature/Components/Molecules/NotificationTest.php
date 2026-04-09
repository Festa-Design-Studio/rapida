<?php

it('renders info notification', function () {
    $view = $this->blade('<x-molecules.notification type="info" message="Data sync complete." />');

    $view->assertSee('Data sync complete.');
    $view->assertSee('bg-rapida-blue-50', false);
    $view->assertSee('border-rapida-blue-100', false);
});

it('renders success notification', function () {
    $view = $this->blade('<x-molecules.notification type="success" message="Report submitted." />');

    $view->assertSee('Report submitted.');
    $view->assertSee('bg-green-50', false);
});

it('renders warning notification', function () {
    $view = $this->blade('<x-molecules.notification type="warning" message="Low battery." />');

    $view->assertSee('Low battery.');
    $view->assertSee('bg-amber-50', false);
});

it('renders error notification', function () {
    $view = $this->blade('<x-molecules.notification type="error" message="Upload failed." />');

    $view->assertSee('Upload failed.');
    $view->assertSee('bg-red-50', false);
});

it('uses alert role for errors and warnings', function () {
    $view = $this->blade('<x-molecules.notification type="error" message="Error" />');

    $view->assertSee('role="alert"', false);
});

it('uses status role for info and success', function () {
    $view = $this->blade('<x-molecules.notification type="info" message="Info" />');

    $view->assertSee('role="status"', false);
    $view->assertSee('aria-live="polite"', false);
});

it('renders dismiss button when dismissible', function () {
    $view = $this->blade('<x-molecules.notification type="info" message="Test" :dismissible="true" />');

    $view->assertSee('aria-label="Dismiss notification"', false);
});

it('renders action button when action provided', function () {
    $view = $this->blade('<x-molecules.notification type="info" message="Test" :action="[\'label\' => \'Retry\', \'url\' => \'/retry\']" />');

    $view->assertSee('Retry');
    $view->assertSee('/retry', false);
});

it('applies rounded-lg with padding and border', function () {
    $view = $this->blade('<x-molecules.notification type="info" message="Test" />');

    $view->assertSee('rounded-lg', false);
    $view->assertSee('p-4', false);
    $view->assertSee('border', false);
});
