@extends('layouts.rapida-docs')

@section('title', 'Toggle — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Toggle</h1>
    <p class="text-body text-text-secondary mt-2">On/off switches for map layer visibility, settings preferences, and binary options that take effect immediately without form submission.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Off State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.toggle name="satellite-view" label="Satellite view" description="Switch map to satellite imagery for better damage visibility" :enabled="false" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — On State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.toggle name="building-footprints" label="Building footprints" description="Show building outlines from OpenStreetMap data" :enabled="true" />
    </div>
</div>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Off</h3>
            <x-atoms.toggle name="toggle-off-demo" label="Offline mode" :enabled="false" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">On</h3>
            <x-atoms.toggle name="toggle-on-demo" label="Offline mode" :enabled="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Disabled</h3>
            <x-atoms.toggle name="toggle-disabled-demo" label="GPS tracking" description="Requires location permission" :enabled="false" :disabled="true" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="switch"</code> with dynamic <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-checked</code>.</li>
        <li>Label linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-labelledby</code> pointing to the label <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">id</code>.</li>
        <li>Keyboard activation: Space key toggles the switch.</li>
        <li>Hidden input syncs the boolean value for form submission.</li>
        <li>Disabled state sets <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-disabled="true"</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">pointer-events-none</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Toggles are used for settings that take immediate effect — no "Save" step needed.</li>
        <li>Clear on/off color states: rapida-blue-700 for on, slate-300 for off.</li>
        <li>Descriptions explain what the toggle does, reducing uncertainty.</li>
        <li>Smooth 200ms transition provides tactile feedback without disorienting animation.</li>
    </ul>
</section>
@endsection
