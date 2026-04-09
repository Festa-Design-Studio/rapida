@extends('layouts.rapida-docs')

@section('title', 'My Reports Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">My Reports</h1>
    <p class="text-body text-text-secondary mt-2">Your reports. Your data. Always yours. Shows all reports the current user has submitted, with sync status and edit access.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('my-reports') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /my-reports
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Persistent navigation</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.community-report-feed</code> — Filtered to user's own reports</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — Sync status summary (synced, pending, draft)</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Sync status summary</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <div class="flex flex-wrap gap-2 mb-4">
            <x-atoms.badge variant="synced">1 Synced</x-atoms.badge>
            <x-atoms.badge variant="pending">1 Pending</x-atoms.badge>
            <x-atoms.badge variant="draft">1 Draft</x-atoms.badge>
        </div>
        @php
            $previewReports = [
                ['damageLevel' => 'partial', 'location' => '14 Elm Street, District 3', 'reporterName' => 'You', 'submittedAt' => '2026-03-27 08:15', 'syncStatus' => 'synced'],
                ['damageLevel' => 'complete', 'location' => 'Al-Nahr Bridge, Route 5', 'reporterName' => 'You', 'submittedAt' => '2026-03-27 07:40', 'syncStatus' => 'pending'],
            ];
        @endphp
        <x-organisms.community-report-feed :reports="$previewReports" />
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Empty state</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.community-report-feed :reports="[]" emptyMessage="You haven't submitted any reports yet." />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>"Your data is always yours" — the header copy establishes data ownership.</li>
        <li>Sync badges give transparent visibility into report delivery status.</li>
        <li>Draft reports are shown — nothing the user created is discarded silently.</li>
        <li>Each report is tappable to view or edit — users maintain control of their submissions.</li>
        <li>"Submit New Report" CTA at bottom respects the user's decision pace.</li>
    </ul>
</section>
@endsection
