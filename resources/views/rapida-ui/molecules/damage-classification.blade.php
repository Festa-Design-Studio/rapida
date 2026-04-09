@extends('layouts.rapida-docs')

@section('title', 'Damage Classification Selector — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Damage Classification Selector</h1>
    <p class="text-body text-text-secondary mt-2">A specialized selector for the three-level damage classification used in RAPIDA reports. Wraps the Radio Group atom with pre-configured options, color dots, and plain-language descriptions to help field reporters classify damage accurately.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — No Selection</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.damage-classification name="demo_damage_1" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Partial Selected</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.damage-classification name="demo_damage_2" value="partial" />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6 max-w-md">
        <div>
            <p class="text-caption text-text-placeholder mb-2">Required with error</p>
            <x-molecules.damage-classification name="demo_damage_3" :required="true" error="Please select a damage level." />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Pre-selected: Complete</p>
            <x-molecules.damage-classification name="demo_damage_4" value="complete" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;fieldset&gt;</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;legend&gt;</code> for semantic grouping.</li>
        <li>Each radio card has an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> with the full damage level name.</li>
        <li>Color dots are decorative — each option also has text label and description.</li>
        <li>Error messages use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code> for immediate screen reader feedback.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Plain-language descriptions help reporters classify damage without technical expertise.</li>
        <li>Three clear levels avoid decision paralysis — not too few, not too many options.</li>
        <li>Color-coded dots (green/amber/red) provide intuitive visual guidance.</li>
        <li>Large card-style radio buttons are easy to tap under stress.</li>
    </ul>
</section>
@endsection
