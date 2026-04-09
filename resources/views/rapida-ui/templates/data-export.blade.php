@extends('layouts.rapida-docs')

@section('title', 'Data Export Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Data Export</h1>
    <p class="text-body text-text-secondary mt-2">Download the data. Protect the people in it. UNDP coordinators can export reports as CSV, GeoJSON, or PDF with field selection and date filtering.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('export') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /export
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — Coordinator navigation</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.data-export</code> — Format selector, filters, field checkboxes, export button</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Export form</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.data-export :formats="['csv', 'geojson', 'pdf']" />
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Available export formats</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-white p-4 text-center">
                <p class="text-h4 font-heading font-semibold text-slate-900">CSV</p>
                <p class="text-caption text-slate-500 mt-1">Spreadsheet format for Excel, Google Sheets</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 text-center">
                <p class="text-h4 font-heading font-semibold text-slate-900">GeoJSON</p>
                <p class="text-caption text-slate-500 mt-1">Geospatial data for GIS tools and maps</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 text-center">
                <p class="text-h4 font-heading font-semibold text-slate-900">PDF</p>
                <p class="text-caption text-slate-500 mt-1">Formatted report for printing and sharing</p>
            </div>
        </div>
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Page header explicitly states exports are anonymized — data protection as a first principle.</li>
        <li>Field checkboxes default to all selected — coordinator can de-select sensitive fields.</li>
        <li>Date range filters prevent bulk exposure of historical data unnecessarily.</li>
        <li>Export is coordinator-only — community members never see this screen.</li>
        <li>Reset Filters ghost button provides a safe undo for filter mistakes.</li>
    </ul>
</section>
@endsection
