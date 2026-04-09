@extends('layouts.rapida-docs')

@section('title', 'Textarea — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Textarea</h1>
    <p class="text-body text-text-secondary mt-2">Multi-line text areas for damage descriptions, landmark directions, and notes. Designed for rapid field entry with character limits to keep reports concise.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — With Character Counter</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.textarea name="damage-desc" label="Damage description" placeholder="Describe the visible damage to this infrastructure..." :maxlength="500" help="Be specific: mention cracks, collapse, flooding depth, etc." />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Landmark Description</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.textarea name="landmark-desc" label="How to find this location" placeholder="Describe nearby landmarks if GPS is unavailable..." :rows="3" help="Example: 200m east of the blue mosque, next to the fuel station." />
    </div>
</div>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Error State</h3>
            <x-atoms.textarea name="textarea-error-demo" label="Damage description" error="Please describe the damage so coordinators can prioritize response." :required="true" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Disabled</h3>
            <x-atoms.textarea name="textarea-disabled-demo" label="Previous notes" value="This report was auto-generated from satellite imagery." :disabled="true" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Character counter is linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-describedby</code> and uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code>.</li>
        <li>Counter color changes at 80% (amber) and 100% (red) to signal limits visually.</li>
        <li>Resize is disabled (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">resize-none</code>) to maintain form layout predictability.</li>
        <li>Error and help text use the same <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-describedby</code> pattern as text inputs.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Character limits (500 chars) reduce the burden of writing long descriptions under stress.</li>
        <li>Placeholder text offers concrete examples to reduce the "blank page" anxiety.</li>
        <li>The landmark fallback field ensures reports are useful even without GPS connectivity.</li>
        <li>Help text provides structured guidance: "mention cracks, collapse, flooding depth."</li>
    </ul>
</section>
@endsection
