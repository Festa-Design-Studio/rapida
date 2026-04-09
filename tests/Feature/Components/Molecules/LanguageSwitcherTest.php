<?php

it('renders with language options', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\', \'ar\' => \'Arabic\', \'ne\' => \'Nepali\']"
        />
    ');

    $view->assertSee('EN');
    $view->assertSee('AR');
    $view->assertSee('NE');
});

it('uses radiogroup role for accessibility', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\']"
        />
    ');

    $view->assertSee('role="radiogroup"', false);
    $view->assertSee('aria-label="Language selection"', false);
});

it('marks current language as checked', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="ar"
            :languages="[\'en\' => \'English\', \'ar\' => \'Arabic\']"
        />
    ');

    $view->assertSee('value="ar"', false);
    $view->assertSee('checked', false);
});

it('composes badge atom for language codes', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\']"
        />
    ');

    $view->assertSee('rounded-full', false);
});

it('has aria-label on each radio input', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\', \'ar\' => \'Arabic\']"
        />
    ');

    $view->assertSee('aria-label="English"', false);
    $view->assertSee('aria-label="Arabic"', false);
});

it('renders with flex-wrap layout', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\']"
        />
    ');

    $view->assertSee('flex flex-wrap gap-2', false);
});
