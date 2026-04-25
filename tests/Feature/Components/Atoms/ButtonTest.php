<?php

it('renders primary button by default', function () {
    $view = $this->blade('<x-atoms.button>Submit Report</x-atoms.button>');

    $view->assertSee('Submit Report');
    $view->assertSee('bg-rapida-blue-900', false);
    $view->assertSee('text-white', false);
    $view->assertSee('type="button"', false);
});

it('renders secondary variant', function () {
    $view = $this->blade('<x-atoms.button variant="secondary">Save Draft</x-atoms.button>');

    $view->assertSee('Save Draft');
    $view->assertSee('border-2', false);
    $view->assertSee('border-rapida-blue-900', false);
    $view->assertSee('text-rapida-blue-900', false);
});

it('renders ghost variant', function () {
    $view = $this->blade('<x-atoms.button variant="ghost">Cancel</x-atoms.button>');

    $view->assertSee('Cancel');
    $view->assertSee('bg-transparent', false);
});

it('renders danger variant', function () {
    $view = $this->blade('<x-atoms.button variant="danger">Delete Report</x-atoms.button>');

    $view->assertSee('Delete Report');
    $view->assertSee('bg-crisis-rose-700', false);
});

it('renders safe-exit variant', function () {
    $view = $this->blade('<x-atoms.button variant="safe-exit">Exit Safely</x-atoms.button>');

    $view->assertSee('Exit Safely');
    $view->assertSee('bg-slate-600', false);
});

it('applies size classes', function () {
    $view = $this->blade('<x-atoms.button size="lg">Next Step</x-atoms.button>');

    $view->assertSee('h-[56px]', false);
});

it('renders as anchor when href is provided', function () {
    $view = $this->blade('<x-atoms.button href="/reports">View Reports</x-atoms.button>');

    $view->assertSee('<a', false);
    $view->assertSee('href="/reports"', false);
    $view->assertDontSee('<button', false);
});

it('includes disabled attribute when disabled', function () {
    $view = $this->blade('<x-atoms.button :disabled="true">Submit</x-atoms.button>');

    $view->assertSee('disabled', false);
    $view->assertSee('disabled:opacity-40', false);
    $view->assertSee('disabled:cursor-not-allowed', false);
});

it('shows spinner when loading', function () {
    $view = $this->blade('<x-atoms.button :loading="true">Submitting...</x-atoms.button>');

    $view->assertSee('animate-spin', false);
    $view->assertSee('aria-live="polite"', false);
    $view->assertSee('disabled', false);
});

it('has focus ring for keyboard accessibility', function () {
    $view = $this->blade('<x-atoms.button>Submit</x-atoms.button>');

    $view->assertSee('focus:ring-2', false);
    $view->assertSee('focus:ring-offset-2', false);
    $view->assertSee('focus:ring-rapida-blue-700', false);
});

it('meets minimum touch target of 48px', function () {
    $view = $this->blade('<x-atoms.button size="md">Tap Me</x-atoms.button>');

    $view->assertSee('h-12', false);
});
