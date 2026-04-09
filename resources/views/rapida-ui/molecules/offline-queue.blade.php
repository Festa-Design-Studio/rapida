@extends('layouts.rapida-docs')

@section('title', 'Offline Queue Indicator — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Offline Queue Indicator</h1>
    <p class="text-body text-text-secondary mt-2">Displays the current connection status and number of reports waiting to sync. Composes Badge, Icon, and Loader atoms. Provides immediate, honest feedback about data transmission in crisis environments with unreliable connectivity.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Online, No Pending</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.offline-queue :pendingCount="0" status="online" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Offline with Pending Reports</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.offline-queue :pendingCount="7" status="offline" />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6">
        <div>
            <p class="text-caption text-text-placeholder mb-2">Online — no pending</p>
            <x-molecules.offline-queue :pendingCount="0" status="online" />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Offline — 3 pending</p>
            <x-molecules.offline-queue :pendingCount="3" status="offline" />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Syncing — 2 pending</p>
            <x-molecules.offline-queue :pendingCount="2" status="syncing" />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Online — 1 pending</p>
            <x-molecules.offline-queue :pendingCount="1" status="online" />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="status"</code> and <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code> for non-intrusive screen reader updates.</li>
        <li>Full <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> describes both connection status and pending count.</li>
        <li>Loader spinner has <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">motion-reduce:animate-pulse</code> for users who prefer reduced motion.</li>
        <li>Icons are decorative (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden</code>) — text labels carry the meaning.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Honest connectivity feedback reduces anxiety — users know their reports are safe locally even when offline.</li>
        <li>Pending count assures users that reports are queued, not lost.</li>
        <li>Syncing animation provides feedback that the system is actively working.</li>
        <li>Status uses familiar green/amber/red color system for instant comprehension.</li>
    </ul>
</section>
@endsection
