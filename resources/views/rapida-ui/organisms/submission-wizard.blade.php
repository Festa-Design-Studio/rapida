@extends('layouts.rapida-docs')

@section('title', 'Submission Wizard — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Submission Wizard</h1>
    <p class="text-body text-text-secondary mt-2">The 5-step report submission flow. Guides community members through photo capture, location entry, damage classification, infrastructure details, and review. Designed for one-handed mobile use in crisis conditions.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Step 1 (Photo)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.submission-wizard :currentStep="1" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Step 3 (Damage Classification)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-organisms.submission-wizard :currentStep="3" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.progress-step</code> — Step indicator (dots + counter variants)</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.form-field-group</code> — Label + input wrapper</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.photo-upload</code> — Camera/gallery photo capture</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.damage-classification</code> — 3-level damage selector</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.infrastructure-type</code> — Infrastructure checkbox grid</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.crisis-type</code> — Crisis category selector</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Navigation and submit actions</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="form"</code> on the wizard container.</li>
        <li>Each step has <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="group"</code> with descriptive <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Navigation buttons have explicit aria-labels for screen readers.</li>
        <li>Progress indicator uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="progressbar"</code> with current/max values.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>"Submit what you have" ghost link on every step allows partial submissions under stress.</li>
        <li>Step-by-step reduces cognitive load — only one task per screen.</li>
        <li>Back button always available so users never feel trapped.</li>
        <li>Review step gives users a sense of control before final submission.</li>
    </ul>
</section>
@endsection
