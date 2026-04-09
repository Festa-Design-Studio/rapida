@extends('layouts.rapida-docs')

@section('title', 'Analytics Dashboard Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">UNDP Analytics Dashboard</h1>
    <p class="text-body text-text-secondary mt-2">Real-time picture of the crisis — for responders. UNDP coordinators see total reports, damage breakdown, sync status, and a live map in a side panel.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /dashboard
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Coordinator variant with Dashboard active</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.analytics-dashboard</code> — KPI cards, sync status, crisis breakdown, recent reports</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.map-organism</code> — Side panel (desktop) / below content (mobile)</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — KPI cards</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        @php
            $previewStats = [
                'totalReports' => 247,
                'byDamageLevel' => ['minimal' => 98, 'partial' => 112, 'complete' => 37],
                'byCrisisType' => ['natural' => 156, 'technological' => 52, 'human-made' => 39],
                'syncedCount' => 231,
                'pendingCount' => 16,
                'recentReports' => [],
            ];
        @endphp
        <x-organisms.analytics-dashboard :stats="$previewStats" />
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Side map panel</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.map-organism height="h-48" />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Dashboard is for coordinators, not community members — separate mental model.</li>
        <li>KPI cards use large display numbers for quick scanning under operational pressure.</li>
        <li>Data refreshes every 30 seconds — coordinators always have a current picture.</li>
        <li>Side-by-side map + data layout on desktop gives spatial and numerical context together.</li>
        <li>Sync status transparency ensures coordinators know what data is confirmed vs. pending.</li>
    </ul>
</section>
@endsection
