@extends('layouts.rapida-docs')

@section('title', 'Submission Confirmation Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Submission Confirmation</h1>
    <p class="text-body text-text-secondary mt-2">Your report was received. You helped. The dignity moment — the person sees their contribution acknowledged and their community impact.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('confirmation') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /confirmation
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms & Molecules Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Persistent navigation</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.submission-confirmation</code> — Report ID, badges, next actions</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.engagement-panel</code> — Community stats and achievements</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Confirmation with sync badge</h2>
    <div class="rounded-lg border border-grey-100 bg-green-50/30 p-padding-card flex justify-center">
        <x-molecules.submission-confirmation
            reportId="RPD-2026-004821"
            damageLevel="partial"
            syncStatus="synced"
            submittedAt="2026-03-27 09:32"
        />
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Engagement panel (community recognition)</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.engagement-panel :communityCount="142" :userReportCount="3" />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Green-50 background conveys success gently — no aggressive celebration.</li>
        <li>Report ID gives the user proof their contribution exists in the system.</li>
        <li>Sync badge shows data reached the server — transparency builds trust.</li>
        <li>Engagement panel recognizes effort without gamifying crisis.</li>
        <li>"Submit Another Report" respects agency — the user decides what to do next.</li>
    </ul>
</section>
@endsection
