@extends('layouts.rapida-docs')

@section('title', 'Community Report Feed — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Community Report Feed</h1>
    <p class="text-body text-text-secondary mt-2">Scrollable list of damage reports submitted by community members. Shows loading skeletons, empty states, and an offline queue indicator. Each report renders as a Damage Report Card molecule.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — With Reports</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        @php
            $sampleReports = [
                ['photo' => 'https://placehold.co/400x200/e2e8f0/64748b?text=Damage', 'damageLevel' => 'partial', 'infrastructureType' => 'Residential', 'location' => '14 Elm Street', 'description' => 'Cracks along walls.', 'reporterName' => 'Ahmed K.', 'submittedAt' => '2026-03-26 10:30', 'syncStatus' => 'synced'],
                ['photo' => 'https://placehold.co/400x200/fef2f2/dc2626?text=Destroyed', 'damageLevel' => 'complete', 'infrastructureType' => 'Hospital', 'location' => 'Central Hospital', 'description' => 'Total structural failure.', 'reporterName' => 'Fatima R.', 'submittedAt' => '2026-03-26 08:15', 'syncStatus' => 'pending'],
            ];
        @endphp
        <x-organisms.community-report-feed :reports="$sampleReports" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Loading State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.community-report-feed :loading="true" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.damage-report-card</code> — Individual report cards</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.offline-queue</code> — Sync status indicator</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.loader</code> — Skeleton card loading states</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Empty state CTA</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — Empty state icon</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="feed"</code> for the report list container.</li>
        <li>Each report card has <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-setsize</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-posinset</code> for screen reader navigation.</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-busy</code> is set during loading states.</li>
        <li>Empty state uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code> for live announcements.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Skeleton loading cards prevent jarring layout shifts and reduce anxiety.</li>
        <li>Empty state encourages participation without pressure.</li>
        <li>Offline queue visibility reassures users their data is safe even without connectivity.</li>
    </ul>
</section>
@endsection
