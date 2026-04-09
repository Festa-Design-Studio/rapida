<?php

it('renders standard mode copy when conflictContext is false', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="false" />'
    );

    $view->assertSee(__('rapida.transparency_standard_1'));
    $view->assertSee(__('rapida.transparency_standard_2'));
    $view->assertSee(__('rapida.transparency_standard_3'));
    $view->assertSee(__('rapida.transparency_standard_4'));
});

it('renders conflict mode copy when conflictContext is true', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="true" />'
    );

    $view->assertSee(__('rapida.transparency_conflict_1'));
    $view->assertSee(__('rapida.transparency_conflict_2'));
    $view->assertSee(__('rapida.transparency_conflict_3'));
    $view->assertSee(__('rapida.transparency_conflict_4'));
});

it('shows Begin report CTA in standard mode', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="false" />'
    );

    $view->assertSee(__('rapida.transparency_standard_cta'));
});

it('shows Submit a report CTA in conflict mode', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="true" />'
    );

    $view->assertSee(__('rapida.transparency_conflict_cta'));
});

it('shows photo + location copy in standard mode', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="false" />'
    );

    $view->assertSee('Your photo + location go to UNDP');
});

it('does not show location mention in conflict mode', function () {
    $view = $this->blade(
        '<x-organisms.transparency-onboarding crisisSlug="test-crisis" :conflictContext="true" />'
    );

    $view->assertDontSee('Your photo + location go to UNDP');
});
