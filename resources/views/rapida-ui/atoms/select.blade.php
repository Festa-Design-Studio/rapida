@extends('layouts.rapida-docs')

@section('title', 'Select — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Select</h1>
    <p class="text-body text-text-secondary mt-2">Native dropdown menus for choosing infrastructure types, sort orders, and filter criteria. Uses the platform-native select for maximum mobile usability.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — With Placeholder</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.select name="sort-order" label="Sort by" placeholder="Select sort order..." :options="['newest' => 'Newest first', 'oldest' => 'Oldest first', 'severity' => 'Highest severity', 'nearest' => 'Nearest to me']" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Pre-Selected Value</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.select name="infra-type" label="Infrastructure type" :options="['bridge' => 'Bridge', 'road' => 'Road', 'school' => 'School', 'hospital' => 'Hospital', 'water' => 'Water supply']" value="road" help="Select the type of infrastructure affected." />
    </div>
</div>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Error</h3>
            <x-atoms.select name="select-error-demo" label="District" placeholder="Select a district..." :options="[]" error="Please select a district to continue." :required="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Disabled</h3>
            <x-atoms.select name="select-disabled-demo" label="Country" :options="['nepal' => 'Nepal']" value="nepal" :disabled="true" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses native <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;select&gt;</code> element for maximum screen reader and mobile compatibility.</li>
        <li>Placeholder option uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">disabled selected</code> so it cannot be re-selected after choosing.</li>
        <li>Custom chevron icon is hidden with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">pointer-events-none</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code>.</li>
        <li>Error and help text linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-describedby</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses platform-native dropdown for familiarity — no custom dropdown menus that may confuse users.</li>
        <li>48px height maintains consistent touch target with other form elements.</li>
        <li>Placeholder text starts with "Select..." to clearly indicate the expected action.</li>
        <li>Short option lists reduce scrolling and decision fatigue.</li>
    </ul>
</section>
@endsection
