@extends('layouts.rapida-docs')

@section('title', 'Form Field Group — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Form Field Group</h1>
    <p class="text-body text-text-secondary mt-2">A wrapper molecule that pairs any input atom with a label, help text, and error message. Ensures consistent spacing, accessibility linking, and validation display across all form fields.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Required Field with Help Text</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.form-field-group label="Location Description" name="location" :required="true" help="Describe the location as precisely as possible.">
            <x-atoms.text-input name="location" placeholder="e.g. 14 Elm Street, near the mosque" />
        </x-molecules.form-field-group>
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Field with Error</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-molecules.form-field-group label="Reporter Name" name="reporter_name" :required="true" error="Reporter name is required.">
            <x-atoms.text-input name="reporter_name" error="Reporter name is required." />
        </x-molecules.form-field-group>
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6 max-w-md">
        <x-molecules.form-field-group label="Required Field" name="required_demo" :required="true">
            <x-atoms.text-input name="required_demo" placeholder="This field is required" />
        </x-molecules.form-field-group>

        <x-molecules.form-field-group label="Optional Field" name="optional_demo" :optional="true">
            <x-atoms.text-input name="optional_demo" placeholder="This field is optional" />
        </x-molecules.form-field-group>

        <x-molecules.form-field-group label="With Help Text" name="help_demo" help="This help text provides additional context.">
            <x-atoms.text-input name="help_demo" />
        </x-molecules.form-field-group>

        <x-molecules.form-field-group label="With Error" name="error_demo" error="This field has a validation error.">
            <x-atoms.text-input name="error_demo" error="This field has a validation error." />
        </x-molecules.form-field-group>

        <x-molecules.form-field-group label="With Textarea" name="textarea_demo" help="Use the textarea atom inside the group.">
            <x-atoms.textarea name="textarea_demo" placeholder="Enter detailed description..." />
        </x-molecules.form-field-group>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Label uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">for</code> attribute linking to the input's <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">id</code>.</li>
        <li>Error messages use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code> for screen reader announcement.</li>
        <li>Required indicator (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">*</code>) is <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code> — the input itself carries <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-required</code>.</li>
        <li>Help text and error are linked via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-describedby</code> on the wrapped input.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Error messages are descriptive and non-blaming: "Reporter name is required" not "You forgot the name."</li>
        <li>Help text provides context for field reporters who may be unfamiliar with the form.</li>
        <li>Optional labels reduce pressure — users know they can skip non-essential fields.</li>
        <li>Consistent layout across all fields reduces cognitive load during stressful data entry.</li>
    </ul>
</section>
@endsection
