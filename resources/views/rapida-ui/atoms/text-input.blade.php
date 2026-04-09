@extends('layouts.rapida-docs')

@section('title', 'Text Input — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Text Input</h1>
    <p class="text-body text-text-secondary mt-2">Single-line text fields for names, addresses, GPS coordinates, and search queries in damage assessment forms.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Default with Help Text</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.text-input name="infra-name" label="Infrastructure name" placeholder="e.g. Main bridge, Sector 4" help="Enter the commonly used name for this infrastructure." />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Error State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.text-input name="infra-name-error" label="Infrastructure name" error="Please enter the infrastructure name so we can locate it." :required="true" />
    </div>
</div>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Default</h3>
            <x-atoms.text-input name="state-default-demo" label="Location" placeholder="Type here..." />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Required</h3>
            <x-atoms.text-input name="state-required-demo" label="Location" placeholder="Required field" :required="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Disabled</h3>
            <x-atoms.text-input name="state-disabled-demo" label="Location" value="Auto-detected location" :disabled="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Readonly</h3>
            <x-atoms.text-input name="state-readonly-demo" label="Report ID" value="RPD-2026-00142" :readonly="true" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Labels are linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">for</code>/<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">id</code> attributes.</li>
        <li>Help text and errors are linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-describedby</code>.</li>
        <li>Error state sets <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-invalid="true"</code> and error messages use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code>.</li>
        <li>Required fields show <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-required="true"</code> and a visual asterisk.</li>
        <li>Readonly fields announce <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-readonly="true"</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Error messages are phrased as helpful guidance, not blame: "Please enter..." not "You forgot..."</li>
        <li>Help text provides concrete examples to reduce decision fatigue.</li>
        <li>Input height is 48px — large enough for shaky or wet fingers.</li>
        <li>Placeholder text is light (slate-400) to avoid confusion with entered values.</li>
    </ul>
</section>
@endsection
