@extends('layouts.rapida-docs')

@section('title', 'Photo Upload — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Photo Upload</h1>
    <p class="text-body text-text-secondary mt-2">Evidence capture component for damage assessment photos. Supports camera capture, gallery selection, and drag-and-drop with file size validation.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Empty Upload Zone</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.photo-upload name="photo-demo-1" label="Damage photo" :required="true" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Error State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-md">
        <x-atoms.photo-upload name="photo-demo-2" label="Damage photo" error="Photo is too large. Maximum size is 10 MB." />
    </div>
</div>

{{-- Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component max-w-md">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Default (10 MB limit)</h3>
            <x-atoms.photo-upload name="photo-variant-default" label="Infrastructure photo" />
        </div>
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Custom Help Text</h3>
            <x-atoms.photo-upload name="photo-variant-custom" label="Before & after comparison" help="Upload a photo showing the current state. A reference photo will be loaded from the database." />
        </div>
    </div>
</section>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-start">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pe-4 font-medium text-text-secondary">State</th>
                    <th class="py-3 font-medium text-text-secondary">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pe-4 font-medium">Empty</td><td class="py-3">Dashed border, camera icon, "Take a photo or choose from gallery"</td></tr>
                <tr><td class="py-3 pe-4 font-medium">Drag over</td><td class="py-3">Border turns rapida-blue, background rapida-blue-50, text changes to "Drop your photo here"</td></tr>
                <tr><td class="py-3 pe-4 font-medium">Preview</td><td class="py-3">Photo thumbnail with "Change" and "Remove" actions below</td></tr>
                <tr><td class="py-3 pe-4 font-medium">Error</td><td class="py-3">Red border, error icon, error message, "Tap to try again"</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>The upload zone is keyboard-focusable with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">tabindex="0"</code> and activates on Enter.</li>
        <li>Dynamic <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> changes with state: "Upload photo" vs "Photo selected. Click to change."</li>
        <li>The hidden file input uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden="true"</code> — interaction flows through the visible zone.</li>
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">capture="environment"</code> to open the rear camera on mobile devices.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Privacy notice: "Your photo will be stored securely and used only for damage assessment by UNDP."</li>
        <li>Large touch target (160px min-height) for field workers wearing gloves or in rain.</li>
        <li>Photos are optional unless explicitly marked required — never force a user to photograph trauma.</li>
        <li>Error messages offer immediate recovery: "Tap to try again" instead of dead-end errors.</li>
    </ul>
</section>
@endsection
