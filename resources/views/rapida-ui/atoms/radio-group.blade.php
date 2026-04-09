@extends('layouts.rapida-docs')

@section('title', 'Radio Group — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Radio Group</h1>
    <p class="text-body text-text-secondary mt-2">Single-choice selection for damage classification, yes/no questions, and mutually exclusive options. Two variants: compact standard list and prominent card layout.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Standard List</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.radio-group
            name="debris-blocking"
            legend="Is debris blocking access?"
            :required="true"
            :options="[
                'yes' => 'Yes — road is impassable',
                'no' => 'No — access is clear',
                'unknown' => 'Unknown — could not verify',
            ]"
            value="yes"
        />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Card Variant (Damage Classification)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.radio-group
            name="damage-level"
            legend="Damage classification"
            variant="card"
            :required="true"
            :options="[
                'minimal' => ['label' => 'Minimal', 'description' => 'Cosmetic damage, infrastructure still usable', 'color' => 'bg-green-500'],
                'partial' => ['label' => 'Partial', 'description' => 'Significant damage, limited functionality', 'color' => 'bg-amber-500'],
                'complete' => ['label' => 'Complete', 'description' => 'Fully destroyed or impassable', 'color' => 'bg-red-600'],
            ]"
            value="partial"
        />
    </div>
</div>

{{-- Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pr-4 font-medium text-text-secondary">Variant</th>
                        <th class="py-3 font-medium text-text-secondary">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pr-4 font-mono text-caption">standard</td><td class="py-3">Quick yes/no questions, simple lists (2-5 options)</td></tr>
                    <tr><td class="py-3 pr-4 font-mono text-caption">card</td><td class="py-3">Important choices with descriptions, damage classification, colored indicators</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Error State</h3>
            <x-atoms.radio-group
                name="radio-error-demo"
                legend="Is this infrastructure operational?"
                :required="true"
                :options="[
                    'yes' => 'Yes',
                    'no' => 'No',
                ]"
                error="Please select whether the infrastructure is operational."
            />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses semantic <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;fieldset&gt;</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;legend&gt;</code> elements.</li>
        <li>Standard variant shows visible radio inputs; card variant uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">sr-only</code> inputs with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Selected state uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">has-[:checked]</code> for CSS-only visual feedback.</li>
        <li>Color dots in card variant are decorative (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code>) — the label text carries the meaning.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Card variant with color dots provides instant visual classification — green/amber/red maps to minimal/partial/complete damage.</li>
        <li>Standard options include "Unknown" where applicable — never force users to guess.</li>
        <li>Each option is a 48px-height touch target in standard variant, and larger in card variant.</li>
        <li>Descriptions in card variant reduce ambiguity: "Cosmetic damage, infrastructure still usable."</li>
    </ul>
</section>
@endsection
