@extends('layouts.rapida-docs')

@section('title', 'Damage Report Card — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Damage Report Card</h1>
    <p class="text-body text-text-secondary mt-2">A card molecule that summarizes a single damage report. Composes Badge atoms for damage level, sync status, and infrastructure type around a photo thumbnail and metadata. Used in report lists, dashboards, and search results.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Standard Card</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-sm">
        <x-molecules.damage-report-card
            photo="https://placehold.co/400x200/e2e8f0/64748b?text=Damage+Photo"
            damageLevel="partial"
            infrastructureType="Residential"
            location="14 Elm Street, District 3"
            description="Cracks along load-bearing walls. Roof partially collapsed in the east wing."
            reporterName="Ahmed K."
            submittedAt="2026-03-26 10:30"
            syncStatus="synced"
        />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Compact Card</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-sm">
        <x-molecules.damage-report-card
            photo="https://placehold.co/400x200/fef2f2/dc2626?text=Destroyed"
            damageLevel="complete"
            infrastructureType="Hospital / Health"
            location="Central Hospital"
            description="Total structural failure — this is hidden in compact mode."
            reporterName="Fatima R."
            submittedAt="2026-03-26 08:15"
            syncStatus="failed"
            variant="compact"
        />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-caption text-text-placeholder mb-2">standard</p>
                <x-molecules.damage-report-card
                    damageLevel="minimal"
                    infrastructureType="School / Education"
                    location="Primary School #7"
                    description="Minor cosmetic damage to exterior walls."
                    reporterName="Nour A."
                    submittedAt="2026-03-25"
                    syncStatus="synced"
                />
            </div>
            <div>
                <p class="text-caption text-text-placeholder mb-2">compact</p>
                <x-molecules.damage-report-card
                    damageLevel="partial"
                    infrastructureType="Road / Bridge"
                    location="Highway 4 Bridge"
                    description="Hidden in compact."
                    reporterName="Omar B."
                    submittedAt="2026-03-25"
                    syncStatus="pending"
                    variant="compact"
                />
            </div>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses semantic <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;article&gt;</code> element with an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> describing the report.</li>
        <li>Photo has descriptive alt text referencing the location.</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">focus-within:ring-2</code> highlights the card when any child element is focused.</li>
        <li>Damage level and sync status are communicated via text labels, not color alone.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Compact variant reduces visual weight for users scanning many reports under stress.</li>
        <li>Damage photos are contained in a fixed-height zone — they cannot dominate the card.</li>
        <li>Description uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">line-clamp-2</code> to prevent overwhelming text walls.</li>
        <li>Sync status badge gives immediate feedback about whether the report reached coordinators.</li>
    </ul>
</section>
@endsection
