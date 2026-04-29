<?php

it('renders dropdown variant by default with all languages', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\', \'fr\' => \'Français\', \'ar\' => \'العربية\']"
        />
    ');

    $view->assertSee('select', false);
    $view->assertSee('English');
    $view->assertSee('Français');
});

it('marks current language as selected in dropdown', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="fr"
            :languages="[\'en\' => \'English\', \'fr\' => \'Français\']"
        />
    ');

    $view->assertSee('value="fr"', false);
    $view->assertSee('selected', false);
});

it('has aria-label on select element', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\']"
        />
    ');

    $view->assertSee('aria-label="Language"', false);
});

it('renders badges variant when specified', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[\'en\' => \'English\', \'ar\' => \'Arabic\']"
            variant="badges"
        />
    ');

    $view->assertSee('role="radiogroup"', false);
    $view->assertSee('EN');
    $view->assertSee('AR');
});

it('renders all 6 UN languages in dropdown', function () {
    $view = $this->blade('
        <x-molecules.language-switcher
            current="en"
            :languages="[
                \'en\' => \'English\',
                \'fr\' => \'Français\',
                \'ar\' => \'العربية\',
                \'zh\' => \'中文\',
                \'ru\' => \'Русский\',
                \'es\' => \'Español\',
            ]"
        />
    ');

    $view->assertSee('English');
    $view->assertSee('中文');
    $view->assertSee('Русский');
    $view->assertSee('Español');
});
