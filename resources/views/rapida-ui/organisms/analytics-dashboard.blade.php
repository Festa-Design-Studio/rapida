@extends('layouts.rapida-docs')

@section('title', 'Analytics Dashboard — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Analytics Dashboard</h1>
    <p class="text-body text-text-secondary mt-2">UNDP coordinator view with KPI cards, damage breakdown by level and crisis type, sync status, and a recent reports list. Designed for rapid situational awareness during crisis response.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — With Data</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        @php
            $demoStats = [
                'totalReports' => 127,
                'byDamageLevel' => ['minimal' => 45, 'partial' => 58, 'complete' => 24],
                'byCrisisType' => ['natural' => 89, 'technological' => 23, 'human-made' => 15],
                'recentReports' => [
                    ['location' => '14 Elm Street', 'damageLevel' => 'partial', 'infrastructureType' => 'Residential', 'syncStatus' => 'synced', 'submittedAt' => '2026-03-26 10:30'],
                    ['location' => 'Central Hospital', 'damageLevel' => 'complete', 'infrastructureType' => 'Hospital', 'syncStatus' => 'synced', 'submittedAt' => '2026-03-26 08:15'],
                ],
                'syncedCount' => 118,
                'pendingCount' => 9,
            ];
        @endphp
        <x-organisms.analytics-dashboard :stats="$demoStats" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Empty State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.analytics-dashboard />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — KPI indicators, damage level tags, sync status</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.damage-report-card</code> — Recent reports list (compact variant)</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.notification</code> — Data refresh notification</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="region"</code> with descriptive label.</li>
        <li>KPI grid uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="list"</code> for structured navigation.</li>
        <li>All numeric values have associated text labels for screen readers.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Dashboard is coordinator-facing, not community-facing — data density is appropriate for trained users.</li>
        <li>Color-coded damage levels match the same palette used throughout the app for consistency.</li>
        <li>Sync status visibility reassures coordinators that field data is arriving.</li>
    </ul>
</section>
@endsection
