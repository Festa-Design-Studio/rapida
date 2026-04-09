@extends('layouts.rapida-docs')

@section('title', 'Infrastructure Type Selector — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Infrastructure Type Selector</h1>
    <p class="text-body text-text-secondary mt-2">A multi-select molecule for categorizing damaged infrastructure. Composes Checkbox atoms in a grid layout with 8 pre-configured infrastructure categories, each with a description to help field reporters classify affected structures.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — No Selection</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg">
        <x-molecules.infrastructure-type name="demo_infra_1" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Pre-selected</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg">
        <x-molecules.infrastructure-type name="demo_infra_2" :values="['residential', 'hospital', 'utility']" />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6 max-w-lg">
        <div>
            <p class="text-caption text-text-placeholder mb-2">Required with error</p>
            <x-molecules.infrastructure-type name="demo_infra_3" :required="true" error="Select at least one infrastructure type." />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;fieldset&gt;</code> with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;legend&gt;</code> for semantic grouping.</li>
        <li>Each checkbox has a visible label and description text.</li>
        <li>Required state is communicated via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-required</code> on the fieldset.</li>
        <li>Error messages use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Descriptions help reporters who may not know technical infrastructure classifications.</li>
        <li>Multi-select allows reporting damage to multiple infrastructure types in a single report.</li>
        <li>Two-column grid layout keeps all options visible without scrolling on most devices.</li>
        <li>Large checkbox touch targets accommodate field use with gloves or trembling hands.</li>
    </ul>
</section>
@endsection
