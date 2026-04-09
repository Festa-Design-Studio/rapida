@extends('layouts.rapida-docs')

@section('title', 'Progress Step — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Progress Step</h1>
    <p class="text-body text-text-secondary mt-2">Wizard progress indicators that show the user where they are in a multi-step damage report flow. Three variants: dots, counter with labels, and progress bar.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Dots Variant (Step 2 of 5)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-atoms.progress-step :current="2" :total="5" variant="dots" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Counter + Labels + Progress Bar</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-element">
        <x-atoms.progress-step :current="3" :total="5" variant="counter" :labels="['Location', 'Infrastructure', 'Damage', 'Photo', 'Review']" />
        <x-atoms.progress-step :current="3" :total="5" variant="bar" />
    </div>
</div>

{{-- Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Dots</h3>
            <x-atoms.progress-step :current="3" :total="5" variant="dots" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Counter</h3>
            <x-atoms.progress-step :current="3" :total="5" variant="counter" :labels="['Location', 'Infrastructure', 'Damage', 'Photo', 'Review']" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Bar</h3>
            <x-atoms.progress-step :current="3" :total="5" variant="bar" />
        </div>

        <div class="overflow-x-auto border-t border-grey-100 pt-inner">
            <table class="w-full text-body-sm text-left">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pr-4 font-medium text-text-secondary">Variant</th>
                        <th class="py-3 font-medium text-text-secondary">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pr-4 font-mono text-caption">dots</td><td class="py-3">Compact progress — mobile wizard header</td></tr>
                    <tr><td class="py-3 pr-4 font-mono text-caption">counter</td><td class="py-3">Verbose — shows step number and label text</td></tr>
                    <tr><td class="py-3 pr-4 font-mono text-caption">bar</td><td class="py-3">Visual fill — pairs well with counter for combined display</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Different Step Positions --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Step Positions</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-element">
        @for($step = 1; $step <= 5; $step++)
            <div class="flex items-center gap-inner">
                <span class="text-caption font-mono text-text-placeholder w-20 shrink-0">Step {{ $step }}/5</span>
                <div class="flex-1">
                    <x-atoms.progress-step :current="$step" :total="5" variant="dots" />
                </div>
            </div>
        @endfor
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Dots variant uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="progressbar"</code> with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-valuenow</code>, <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-valuemin</code>, <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-valuemax</code>.</li>
        <li>Each dot has an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>: "Step 2: Complete" or "Step 3: Current."</li>
        <li>Counter variant uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code> so step changes are announced.</li>
        <li>Bar variant reports percentage via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-valuenow</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Progress indicators reduce anxiety by showing the user how much is left to do.</li>
        <li>5-step maximum keeps the form from feeling overwhelming.</li>
        <li>Completed steps are visually distinct (solid rapida-blue) from remaining steps (grey).</li>
        <li>The current step is emphasized with a larger dot and focus ring — "You are here."</li>
    </ul>
</section>
@endsection
