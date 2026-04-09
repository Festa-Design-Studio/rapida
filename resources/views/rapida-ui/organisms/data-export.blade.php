@extends('layouts.rapida-docs')

@section('title', 'Data Export — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Data Export Interface</h1>
    <p class="text-body text-text-secondary mt-2">Export configuration panel for UNDP coordinators. Supports CSV, GeoJSON, and PDF formats with date range filtering, damage level filtering, and field selection. Enables downstream analysis and reporting workflows.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Default Formats</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.data-export />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — CSV Only</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.data-export :formats="['csv']" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.form-field-group</code> — Label + input wrappers for format, dates, filters</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.checkbox</code> — Field selection checklist</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.select</code> — Format and damage level dropdowns</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.text-input</code> — Date range inputs</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Export and reset actions</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.notification</code> — Export progress notification</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="region"</code> with descriptive label.</li>
        <li>Field selection fieldset has explicit <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Export button has <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> describing the action.</li>
        <li>All form controls inherit accessible patterns from composed atoms.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Coordinator-facing interface with appropriate complexity for trained users.</li>
        <li>All fields pre-checked by default — users remove what they do not need rather than building from scratch.</li>
        <li>Export progress notification provides reassurance during long operations.</li>
    </ul>
</section>
@endsection
