@extends('layouts.rapida-docs')

@section('title', 'Report Detail Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Report Detail</h1>
    <p class="text-body text-text-secondary mt-2">One incident. Full context. Correctable. Shows the complete report with photo, metadata, location map, and version history.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('report-detail', ['id' => 'RPD-2026-004821']) }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /report/RPD-2026-004821
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Persistent navigation</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.map-organism</code> — Small map showing pin location</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.report-version-history</code> — Edit timeline with restore</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — Damage level and sync status</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Report header with metadata</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card space-y-4">
        <div class="w-full h-40 rounded-lg bg-slate-200 flex items-center justify-center">
            <x-atoms.icon name="camera" size="xl" class="text-slate-400" />
        </div>
        <div class="flex flex-wrap gap-2">
            <x-atoms.badge variant="partial">Partial</x-atoms.badge>
            <x-atoms.badge variant="synced">Synced</x-atoms.badge>
        </div>
        <h3 class="text-h3 font-heading font-semibold text-slate-900">14 Elm Street, District 3</h3>
        <p class="text-body-sm text-slate-600">Road surface cracked after flooding. Two lanes blocked by debris.</p>
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Version history timeline</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.report-version-history :versions="[
            ['version' => 2, 'status' => 'current', 'editedBy' => 'Ahmed K.', 'editedAt' => '2026-03-27 10:30', 'changes' => 'Updated description with sidewalk details.'],
            ['version' => 1, 'status' => 'original', 'editedBy' => 'Ahmed K.', 'editedAt' => '2026-03-27 08:15', 'changes' => 'Initial report submitted.'],
        ]" />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Photo at top grounds the user in the specific incident — visual context first.</li>
        <li>Edit button empowers correction — mistakes are fixable, not permanent.</li>
        <li>Version history shows full transparency of who changed what and when.</li>
        <li>Mini map confirms the location visually — reduces ambiguity.</li>
        <li>"Back to My Reports" provides a clear escape path.</li>
    </ul>
</section>
@endsection
