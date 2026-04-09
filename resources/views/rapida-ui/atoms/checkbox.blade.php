@extends('layouts.rapida-docs')

@section('title', 'Checkbox — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Checkbox</h1>
    <p class="text-body text-text-secondary mt-2">Multi-select inputs for infrastructure types, affected services, and confirmation agreements. Each checkbox is a self-contained label-input pair with optional description.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Multi-Select Infrastructure Type</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <fieldset class="flex flex-col gap-3">
            <legend class="text-label font-medium text-slate-700 mb-1">Affected infrastructure types</legend>
            <x-atoms.checkbox name="infra_types[]" value="road" label="Road or highway" description="Paved or unpaved road surface" :checked="true" />
            <x-atoms.checkbox name="infra_types[]" value="bridge" label="Bridge or overpass" description="Spanning a river, valley, or road" />
            <x-atoms.checkbox name="infra_types[]" value="water" label="Water supply" description="Pipes, pumps, or treatment facility" />
        </fieldset>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Single Confirmation</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.checkbox name="confirm" label="I confirm this report is accurate to the best of my knowledge" :required="true" />
    </div>
</div>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Checked</h3>
            <x-atoms.checkbox name="state-checked" value="1" label="Active option" :checked="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Unchecked</h3>
            <x-atoms.checkbox name="state-unchecked" value="1" label="Inactive option" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Disabled</h3>
            <x-atoms.checkbox name="state-disabled" value="1" label="Cannot be changed" :disabled="true" :checked="true" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Each checkbox has a unique <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">id</code> derived from name + value.</li>
        <li>The entire label row is clickable, not just the checkbox input.</li>
        <li>Selected state is visually indicated with rapida-blue border and background via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">has-[:checked]</code>.</li>
        <li>Required checkboxes set <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-required="true"</code>.</li>
        <li>Disabled state adds <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">opacity-40</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">pointer-events-none</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Large touch area (full row) accommodates stressed, shaky input.</li>
        <li>Descriptions help users understand options without external reference.</li>
        <li>Confirmation checkboxes use plain language: "I confirm..." not legalese.</li>
        <li>Multi-select lists reduce form steps — one screen instead of multiple.</li>
    </ul>
</section>
@endsection
