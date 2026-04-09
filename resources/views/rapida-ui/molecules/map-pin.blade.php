@extends('layouts.rapida-docs')

@section('title', 'Map Pin & Marker Cluster — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Map Pin & Marker Cluster</h1>
    <p class="text-body text-text-secondary mt-2">Map markers that indicate damage report locations. Available as individual pins color-coded by damage level, or as cluster markers showing the count of reports in an area. Used on the coordinator dashboard map.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Individual Pins</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-center gap-8">
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="minimal" />
                <span class="text-caption text-text-placeholder">Minimal</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="partial" />
                <span class="text-caption text-text-placeholder">Partial</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="complete" />
                <span class="text-caption text-text-placeholder">Complete</span>
            </div>
        </div>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Marker Clusters</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-center gap-8">
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="minimal" variant="cluster" :count="4" />
                <span class="text-caption text-text-placeholder">4 minimal</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="partial" variant="cluster" :count="12" />
                <span class="text-caption text-text-placeholder">12 partial</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-molecules.map-pin damageLevel="complete" variant="cluster" :count="27" />
                <span class="text-caption text-text-placeholder">27 complete</span>
            </div>
        </div>
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pr-4 font-medium text-text-secondary">Variant</th>
                        <th class="py-3 pr-4 font-medium text-text-secondary">Damage Level</th>
                        <th class="py-3 font-medium text-text-secondary">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pr-4 font-mono text-caption">pin</td><td class="py-3 pr-4">minimal/partial/complete</td><td class="py-3">Single report location on map</td></tr>
                    <tr><td class="py-3 pr-4 font-mono text-caption">cluster</td><td class="py-3 pr-4">minimal/partial/complete</td><td class="py-3">Multiple reports in one area, shows count</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Both variants use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="img"</code> with descriptive <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Pin animation is decorative (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code>).</li>
        <li>Cluster markers are 48px diameter, meeting the minimum touch target.</li>
        <li>Color is paired with text (cluster count) and shape (pin dot) for multiple information channels.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Intuitive green/amber/red color coding matches the damage level system used throughout RAPIDA.</li>
        <li>Cluster markers prevent visual overload when many reports exist in one area.</li>
        <li>Subtle ping animation on pins draws attention without being alarming.</li>
        <li>Large cluster markers are easy to tap on mobile devices in field conditions.</li>
    </ul>
</section>
@endsection
