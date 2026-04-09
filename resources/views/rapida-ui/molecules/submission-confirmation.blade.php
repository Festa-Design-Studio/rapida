@extends('layouts.rapida-docs')

@section('title', 'Submission Confirmation — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Submission Confirmation</h1>
    <p class="text-body text-text-secondary mt-2">A full-width confirmation message shown after a damage report is submitted. Composes Icon, Badge, and Button atoms into a centered, reassuring layout. Provides immediate feedback that the report was recorded and its current sync status.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Synced Report</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.submission-confirmation
            reportId="RPT-2026-042"
            submittedAt="2026-03-26 10:30"
            damageLevel="partial"
            syncStatus="synced"
        />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Pending Sync</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.submission-confirmation
            reportId="RPT-2026-043"
            submittedAt="2026-03-26 11:15"
            damageLevel="complete"
            syncStatus="pending"
        />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Damage Levels</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6">
        <x-molecules.submission-confirmation
            reportId="RPT-001"
            damageLevel="minimal"
            syncStatus="synced"
        />
        <x-molecules.submission-confirmation
            reportId="RPT-002"
            damageLevel="partial"
            syncStatus="pending"
        />
        <x-molecules.submission-confirmation
            reportId="RPT-003"
            damageLevel="complete"
            syncStatus="failed"
        />
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code> for screen reader announcement.</li>
        <li>Check circle icon is decorative — the heading text "Report Submitted" carries the meaning.</li>
        <li>Damage level and sync status are communicated via Badge atom text labels.</li>
        <li>Action buttons ("Submit Another Report", "View Report") have clear, descriptive labels.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Green background and check icon provide positive reinforcement — the reporter's effort was recorded.</li>
        <li>Report ID gives a concrete reference number, building trust that the report exists in the system.</li>
        <li>Sync status badge provides honest feedback about whether the report reached coordinators.</li>
        <li>Two clear action buttons prevent the "now what?" feeling after form submission.</li>
    </ul>
</section>
@endsection
