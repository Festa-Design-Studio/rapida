@extends('layouts.rapida-docs')

@section('title', 'Map Organism — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Map Organism</h1>
    <p class="text-body text-text-secondary mt-2">Container for the MapLibre GL damage map. Provides zoom controls, layer toggles, a damage-level legend, and Map Pin molecules for each report. Currently renders a styled placeholder until MapLibre JS is integrated.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Default Height (500px)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.map-organism />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Custom Height (300px)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.map-organism height="h-[300px]" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.map-pin</code> — Damage-colored map markers</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Zoom in, zoom out, layer toggle</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — Map placeholder icon</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="region"</code> with descriptive label for the map area.</li>
        <li>Zoom and layer controls have explicit <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> attributes.</li>
        <li>Map pins use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="img"</code> with damage level descriptions.</li>
        <li>Legend uses text labels alongside color indicators — never color alone.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Map has a contained, bounded area — damage visuals do not spill into surrounding UI.</li>
        <li>Neutral placeholder colors prevent distress when the map is loading.</li>
        <li>Pin pulsing animation is subtle and can be disabled via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">motion-reduce</code>.</li>
    </ul>
</section>
@endsection
