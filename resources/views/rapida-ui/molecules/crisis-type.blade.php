@extends('layouts.rapida-docs')

@section('title', 'Crisis Type Selector — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Crisis Type Selector</h1>
    <p class="text-body text-text-secondary mt-2">A single-select molecule for identifying the type of crisis causing the damage. Wraps the Radio Group atom with three pre-configured crisis categories. This classification helps coordinators route reports to the appropriate response teams.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — No Selection</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.crisis-type name="demo_crisis_1" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Natural Disaster Selected</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.crisis-type name="demo_crisis_2" value="natural" />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6 max-w-md">
        <div>
            <p class="text-caption text-text-placeholder mb-2">Required with error</p>
            <x-molecules.crisis-type name="demo_crisis_3" :required="true" error="Please select a crisis type." />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Pre-selected: Human-made</p>
            <x-molecules.crisis-type name="demo_crisis_4" value="human-made" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;fieldset&gt;</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;legend&gt;</code> for semantic grouping.</li>
        <li>Each radio card has an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> with the crisis type name.</li>
        <li>Descriptions provide context without requiring domain expertise.</li>
        <li>Error messages use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code>.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Three options prevent decision fatigue in high-stress situations.</li>
        <li>Descriptions use neutral, factual language — not emotionally charged terminology.</li>
        <li>"Human-made / Conflict" uses careful phrasing that avoids political judgment.</li>
        <li>Card-style radio buttons provide clear, tappable targets.</li>
    </ul>
</section>
@endsection
