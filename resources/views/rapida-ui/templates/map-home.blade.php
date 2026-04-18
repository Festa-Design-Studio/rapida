@extends('layouts.rapida-docs')

@section('title', 'Map Home Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Map Home</h1>
    <p class="text-body text-text-secondary mt-2">A living map of the crisis zone — community-built. The default screen after onboarding. Shows all submitted reports as damage-colored pins.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('map-home') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Sticky top bar with nav, language, Safe Exit</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.map-organism</code> — Full-height map with damage pins and legend</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.community-report-feed</code> — Bottom sheet with recent reports</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Floating "Report Damage" CTA</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Map with floating button</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card relative" style="min-height: 300px;">
        <x-organisms.map-organism height="h-64" />
        <div class="absolute bottom-36 sm:bottom-2.5 left-1/2 -translate-x-1/2 z-10">
            <x-atoms.button variant="primary" size="lg" class="shadow-lg">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Report Damage
            </x-atoms.button>
        </div>
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Community report feed (bottom sheet)</h2>
    <div class="rounded-lg border border-grey-100 bg-white p-padding-card">
        @php
            $sampleReports = [
                ['damageLevel' => 'partial', 'infrastructureType' => 'road', 'location' => '14 Elm St', 'reporterName' => 'Ahmed K.', 'submittedAt' => '2026-03-27 08:15', 'syncStatus' => 'synced'],
                ['damageLevel' => 'complete', 'infrastructureType' => 'bridge', 'location' => 'Al-Nahr Bridge', 'reporterName' => 'Fatima R.', 'submittedAt' => '2026-03-27 07:40', 'syncStatus' => 'synced'],
            ];
        @endphp
        <x-organisms.community-report-feed :reports="$sampleReports" />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Map fills the viewport — the crisis zone is always visible, grounding the user in place.</li>
        <li>"Report Damage" button floats above the map with clear contrast and shadow for affordance.</li>
        <li>Community Report Feed builds trust — others have reported successfully.</li>
        <li>Navigation header includes Safe Exit button for users who need to leave quickly.</li>
        <li>No login wall between the user and the action of reporting.</li>
    </ul>
</section>
@endsection
