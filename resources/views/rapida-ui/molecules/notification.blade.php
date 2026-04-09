@extends('layouts.rapida-docs')

@section('title', 'Notification & Alert — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Notification & Alert</h1>
    <p class="text-body text-text-secondary mt-2">Contextual messages for feedback, warnings, and errors. Composes Icon and Button atoms. Supports four severity types, optional dismiss functionality, and action buttons. Critical for communicating system status in crisis reporting workflows.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Success Notification</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg">
        <x-molecules.notification type="success" message="Your damage report has been submitted and synced successfully." />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Error with Action</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg">
        <x-molecules.notification
            type="error"
            message="Failed to sync 3 reports. Check your connection and try again."
            :action="['label' => 'Retry Sync', 'url' => '#']"
            :dismissible="true"
        />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Types</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-4 max-w-lg">
        <x-molecules.notification type="info" message="Your session will expire in 10 minutes. Save your work." />
        <x-molecules.notification type="success" message="Report RPT-2026-042 has been verified by a coordinator." />
        <x-molecules.notification type="warning" message="You are working offline. Reports will sync when connection is restored." />
        <x-molecules.notification type="error" message="Photo upload failed. The file may be too large." />
    </div>
</section>

{{-- Dismissible --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Dismissible</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg">
        <x-molecules.notification type="info" message="Click the X to dismiss this notification." :dismissible="true" />
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Error and warning notifications use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code> for immediate screen reader announcement.</li>
        <li>Info and success notifications use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code> with <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code>.</li>
        <li>Dismiss button has <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label="Dismiss notification"</code>.</li>
        <li>Icons are decorative — the text message carries the full meaning.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Error messages include actionable guidance: "Check your connection and try again" not just "Failed."</li>
        <li>Warning notifications use amber, not red, to avoid triggering alarm responses.</li>
        <li>Dismissible notifications give users control over their visual environment.</li>
        <li>Action buttons provide a clear next step rather than leaving users stranded with an error.</li>
    </ul>
</section>
@endsection
