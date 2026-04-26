@extends('layouts.rapida-docs')

@section('title', 'Button — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Button</h1>
    <p class="text-body text-text-secondary mt-2">Buttons trigger actions. In crisis reporting, every button press may mean the difference between a report reaching coordinators or being lost.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Primary Submit</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-atoms.button variant="primary" type="submit">Submit Report</x-atoms.button>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Safe Exit</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-atoms.button variant="safe-exit">Exit Safely</x-atoms.button>
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.button variant="primary">Primary</x-atoms.button>
            <x-atoms.button variant="secondary">Secondary</x-atoms.button>
            <x-atoms.button variant="ghost">Ghost</x-atoms.button>
            <x-atoms.button variant="danger">Danger</x-atoms.button>
            <x-atoms.button variant="safe-exit">Safe Exit</x-atoms.button>
            <x-atoms.button variant="icon-only" size="icon">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </x-atoms.button>
        </div>

        <div class="mt-inner pt-inner border-t border-grey-100">
            <div class="overflow-x-auto">
                <table class="w-full text-body-sm text-start">
                    <thead>
                        <tr class="border-b border-grey-100">
                            <th class="py-3 pe-4 font-medium text-text-secondary">Variant</th>
                            <th class="py-3 font-medium text-text-secondary">Usage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr><td class="py-3 pe-4 font-mono text-caption">primary</td><td class="py-3">Submit report, confirm action, main CTA</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">secondary</td><td class="py-3">Secondary actions like "Save draft", "Add another"</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">ghost</td><td class="py-3">Tertiary actions, cancel, back navigation</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">danger</td><td class="py-3">Destructive actions like "Delete report"</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">safe-exit</td><td class="py-3">Always-visible escape from a form or flow</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">icon-only</td><td class="py-3">Map controls, toolbar actions, close dialogs</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Sizes --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Sizes</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.button variant="primary" size="sm">Small</x-atoms.button>
            <x-atoms.button variant="primary" size="md">Medium</x-atoms.button>
            <x-atoms.button variant="primary" size="lg">Large</x-atoms.button>
        </div>
        <div class="mt-inner pt-inner border-t border-grey-100">
            <div class="overflow-x-auto">
                <table class="w-full text-body-sm text-start">
                    <thead>
                        <tr class="border-b border-grey-100">
                            <th class="py-3 pe-4 font-medium text-text-secondary">Size</th>
                            <th class="py-3 pe-4 font-medium text-text-secondary">Height</th>
                            <th class="py-3 font-medium text-text-secondary">Usage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr><td class="py-3 pe-4 font-mono text-caption">sm</td><td class="py-3 pe-4">40px</td><td class="py-3">Inline actions, table rows</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">md</td><td class="py-3 pe-4">48px</td><td class="py-3">Default — forms, dialogs</td></tr>
                        <tr><td class="py-3 pe-4 font-mono text-caption">lg</td><td class="py-3 pe-4">56px</td><td class="py-3">Primary CTA, mobile full-width</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.button variant="primary">Default</x-atoms.button>
            <x-atoms.button variant="primary" :disabled="true">Disabled</x-atoms.button>
            <x-atoms.button variant="primary" :loading="true">Submitting...</x-atoms.button>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>All buttons receive visible focus rings via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">focus:ring-2</code>.</li>
        <li>Loading state sets <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code> and disables the button.</li>
        <li>Link buttons (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">href</code>) use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-disabled</code> instead of the HTML <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">disabled</code> attribute.</li>
        <li>Icon-only buttons must include an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> describing the action.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>The safe-exit button uses neutral slate-600, not alarming red — it should feel calm and easy to press.</li>
        <li>Loading state prevents accidental double-submission of crisis reports.</li>
        <li>Buttons are 48px minimum height for stressed users with trembling hands.</li>
        <li>Labels use clear, action-oriented language: "Submit Report" not "Submit".</li>
    </ul>
</section>
@endsection
