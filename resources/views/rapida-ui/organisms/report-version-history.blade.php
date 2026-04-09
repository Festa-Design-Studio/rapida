@extends('layouts.rapida-docs')

@section('title', 'Report Version History — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Report Version History</h1>
    <p class="text-body text-text-secondary mt-2">Timeline of edits to a single damage report. Shows who edited, when, and what changed. The current version is highlighted; previous versions can be viewed or restored. Provides transparency and accountability for report accuracy.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Default Timeline</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-xl">
        <x-organisms.report-version-history />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Single Version</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-xl">
        @php
            $singleVersion = [[
                'version' => 1,
                'status' => 'current',
                'editedBy' => 'Nour A.',
                'editedAt' => '2026-03-26 09:00',
                'changes' => 'Initial report submitted. Photo, location, and damage classification provided.',
            ]];
        @endphp
        <x-organisms.report-version-history :versions="$singleVersion" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — Version status (Current, Previous, Original)</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — History icon in header</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — View and Restore actions</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="region"</code> with descriptive label on the container.</li>
        <li>Timeline uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="list"</code> / <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="listitem"</code> for structure.</li>
        <li>Each version card has an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> with version number and status.</li>
        <li>View/Restore buttons have specific aria-labels including the version number.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Transparency in edits builds trust — reporters can see their work is respected.</li>
        <li>Restore capability means mistakes can be undone, reducing anxiety about edits.</li>
        <li>Clear timeline visual reduces cognitive load when reviewing changes.</li>
    </ul>
</section>
@endsection
