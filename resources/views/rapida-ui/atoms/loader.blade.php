@extends('layouts.rapida-docs')

@section('title', 'Loader — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Loader</h1>
    <p class="text-body text-text-secondary mt-2">Loading indicators for async operations: spinners for indeterminate waits, skeleton placeholders for content layout, and progress rings for determinate uploads.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Spinners in 3 Sizes</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-center gap-gap-component">
            <div class="flex flex-col items-center gap-2">
                <x-atoms.loader variant="spinner" size="sm" />
                <span class="text-caption text-text-placeholder">sm</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.loader variant="spinner" size="md" />
                <span class="text-caption text-text-placeholder">md</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.loader variant="spinner" size="lg" message="Loading reports..." />
                <span class="text-caption text-text-placeholder">lg (inline)</span>
            </div>
        </div>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Skeleton Card + Progress Ring</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-inner items-start">
            <x-atoms.loader variant="skeleton-card" />
            <div class="flex flex-col items-center gap-3 p-inner">
                <x-atoms.loader variant="progress-ring" :percentage="64" />
                <span class="text-body-sm text-text-secondary">Uploading photo...</span>
            </div>
        </div>
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Spinner</h3>
            <div class="flex items-center gap-3">
                <x-atoms.loader variant="spinner" size="md" />
                <span class="text-body-sm text-text-secondary">Indeterminate loading — used when duration is unknown.</span>
            </div>
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Skeleton Text</h3>
            <div class="max-w-sm">
                <x-atoms.loader variant="skeleton-text" />
            </div>
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Skeleton Card</h3>
            <div class="max-w-sm">
                <x-atoms.loader variant="skeleton-card" />
            </div>
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Skeleton Image</h3>
            <div class="max-w-sm">
                <x-atoms.loader variant="skeleton-image" />
            </div>
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Progress Ring</h3>
            <div class="flex items-center gap-inner">
                <x-atoms.loader variant="progress-ring" :percentage="25" />
                <x-atoms.loader variant="progress-ring" :percentage="50" />
                <x-atoms.loader variant="progress-ring" :percentage="75" />
                <x-atoms.loader variant="progress-ring" :percentage="100" />
            </div>
        </div>
    </div>
</section>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-start">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pe-4 font-medium text-text-secondary">Variant</th>
                        <th class="py-3 pe-4 font-medium text-text-secondary">Aria Role</th>
                        <th class="py-3 font-medium text-text-secondary">Reduced Motion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pe-4">Spinner</td><td class="py-3 pe-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code></td><td class="py-3">Switches to <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">animate-pulse</code></td></tr>
                    <tr><td class="py-3 pe-4">Skeleton text</td><td class="py-3 pe-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-busy="true"</code></td><td class="py-3">Static grey blocks</td></tr>
                    <tr><td class="py-3 pe-4">Skeleton card</td><td class="py-3 pe-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-busy="true"</code></td><td class="py-3">Static grey blocks</td></tr>
                    <tr><td class="py-3 pe-4">Skeleton image</td><td class="py-3 pe-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-busy="true"</code></td><td class="py-3">Static grey block</td></tr>
                    <tr><td class="py-3 pe-4">Progress ring</td><td class="py-3 pe-4">Percentage text</td><td class="py-3">Static ring, no transition</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Spinners use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code> with a descriptive <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Skeleton placeholders use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-busy="true"</code> to indicate loading content.</li>
        <li>Progress ring shows percentage as visible text and is read by screen readers.</li>
        <li>Full-screen overlay spinner traps focus and announces the loading message.</li>
        <li>All animations respect <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">prefers-reduced-motion</code> via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">motion-reduce:</code> classes.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Loading indicators prevent the "is it working?" anxiety — especially critical on slow connections.</li>
        <li>Skeleton cards show the expected layout shape, preparing the user for what is coming.</li>
        <li>Progress rings with percentage give concrete feedback during photo uploads over slow networks.</li>
        <li>The full-screen overlay (lg spinner with message) is used sparingly — only for critical waits like form submission.</li>
    </ul>
</section>
@endsection
