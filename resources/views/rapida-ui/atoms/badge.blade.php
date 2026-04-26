@extends('layouts.rapida-docs')

@section('title', 'Badge — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Badge</h1>
    <p class="text-body text-text-secondary mt-2">Status indicators for damage levels, sync states, verification, and metadata labels. Badges are non-interactive — they communicate state at a glance.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Damage Level Badges</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.badge variant="minimal">Minimal</x-atoms.badge>
            <x-atoms.badge variant="partial">Partial</x-atoms.badge>
            <x-atoms.badge variant="complete">Complete</x-atoms.badge>
        </div>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Sync State & Metadata Badges</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.badge variant="synced">Synced</x-atoms.badge>
            <x-atoms.badge variant="pending">Pending sync</x-atoms.badge>
            <x-atoms.badge variant="failed">Sync failed</x-atoms.badge>
            <x-atoms.badge variant="verified">Verified</x-atoms.badge>
            <x-atoms.badge variant="language">AR</x-atoms.badge>
        </div>
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner mb-inner">
            <x-atoms.badge variant="default">Default</x-atoms.badge>
            <x-atoms.badge variant="minimal">Minimal</x-atoms.badge>
            <x-atoms.badge variant="partial">Partial</x-atoms.badge>
            <x-atoms.badge variant="complete">Complete</x-atoms.badge>
            <x-atoms.badge variant="synced">Synced</x-atoms.badge>
            <x-atoms.badge variant="pending">Pending</x-atoms.badge>
            <x-atoms.badge variant="failed">Failed</x-atoms.badge>
            <x-atoms.badge variant="draft">Draft</x-atoms.badge>
            <x-atoms.badge variant="verified">Verified</x-atoms.badge>
            <x-atoms.badge variant="info">Info</x-atoms.badge>
            <x-atoms.badge variant="language">EN</x-atoms.badge>
        </div>

        <div class="overflow-x-auto border-t border-grey-100 pt-inner">
            <table class="w-full text-body-sm text-start">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pe-4 font-medium text-text-secondary">Variant</th>
                        <th class="py-3 pe-4 font-medium text-text-secondary">Color</th>
                        <th class="py-3 font-medium text-text-secondary">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pe-4 font-mono text-caption">minimal</td><td class="py-3 pe-4">Green + dot</td><td class="py-3">Minimal damage classification</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">partial</td><td class="py-3 pe-4">Amber + dot</td><td class="py-3">Partial damage classification</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">complete</td><td class="py-3 pe-4">Red + dot</td><td class="py-3">Complete destruction classification</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">synced</td><td class="py-3 pe-4">Green</td><td class="py-3">Report successfully uploaded</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">pending</td><td class="py-3 pe-4">Amber</td><td class="py-3">Queued for upload</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">failed</td><td class="py-3 pe-4">Red</td><td class="py-3">Upload failed</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">draft</td><td class="py-3 pe-4">Slate</td><td class="py-3">Report saved locally, not submitted</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">verified</td><td class="py-3 pe-4">RAPIDA Blue (solid)</td><td class="py-3">Coordinator-verified report</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">info</td><td class="py-3 pe-4">RAPIDA Blue (subtle)</td><td class="py-3">Informational labels</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">language</td><td class="py-3 pe-4">Slate</td><td class="py-3">Language code indicators (EN, AR, NE)</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Sizes --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Sizes</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <div class="flex flex-col items-center gap-2">
                <x-atoms.badge variant="minimal">Default size</x-atoms.badge>
                <span class="text-caption text-text-placeholder">default</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.badge variant="minimal" size="lg">Large size</x-atoms.badge>
                <span class="text-caption text-text-placeholder">lg</span>
            </div>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Badges are non-interactive <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;span&gt;</code> elements — they have no focus state.</li>
        <li>Color dots are decorative (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code>) — the text label carries the meaning.</li>
        <li>Damage level badges pair color with a colored dot and text — three channels of information.</li>
        <li>Badge text should be descriptive: "Sync failed" not just "Failed."</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Damage levels use intuitive green/amber/red progression — universally understood.</li>
        <li>Sync state badges provide immediate feedback on connectivity — critical in field conditions.</li>
        <li>"Verified" badge uses a solid rapida-blue background to stand out as authoritative confirmation.</li>
        <li>Badge text avoids jargon — "Pending sync" not "Queued for remote persistence."</li>
    </ul>
</section>
@endsection
