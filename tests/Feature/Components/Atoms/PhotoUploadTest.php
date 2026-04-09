<?php

it('renders empty upload zone', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" label="Photo of damage" :required="true" />');

    $view->assertSee('Photo of damage');
    $view->assertSee('Take a photo or choose from gallery');
    $view->assertSee('role="button"', false);
    $view->assertSee('tabindex="0"', false);
});

it('renders with required indicator', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" label="Photo" :required="true" />');

    $view->assertSee('*');
});

it('uses camera capture for mobile', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('capture="environment"', false);
});

it('accepts correct file formats', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('image/jpeg', false);
    $view->assertSee('image/png', false);
    $view->assertSee('image/webp', false);
    $view->assertSee('image/heic', false);
});

it('has hidden file input for accessibility', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('sr-only', false);
    $view->assertSee('type="file"', false);
});

it('shows privacy help text by default', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('stored securely and used only for damage assessment');
});

it('renders dashed border on empty state', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('border-dashed', false);
    $view->assertSee('rounded-xl', false);
});

it('shows max file size in help text', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" :maxSize="5" />');

    $view->assertSee('max 5 MB');
});

it('includes compressing state template', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('Optimizing your photo...');
    $view->assertSee('Compressing for faster upload');
});

it('includes compression info display in preview state', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('Photo optimized for fast upload');
});

it('includes compressImage method in alpine data', function () {
    $view = $this->blade('<x-atoms.photo-upload name="photo" />');

    $view->assertSee('compressImage', false);
    $view->assertSee('getCompressionQuality', false);
    $view->assertSee('compressionInfo', false);
});
