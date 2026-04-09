@extends('layouts.rapida-docs')

@section('title', 'Engagement Panel — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Engagement & Recognition Panel</h1>
    <p class="text-body text-text-secondary mt-2">Community contribution stats and recognition badges. Shows how many community members have submitted reports, the user's own contribution count, and earned achievements. Encourages participation through positive reinforcement rather than gamification pressure.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Active Contributor</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg mx-auto">
        <x-organisms.engagement-panel :communityCount="247" :userReportCount="8" />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — New User</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card max-w-lg mx-auto">
        <x-organisms.engagement-panel :communityCount="12" :userReportCount="0" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — Earned/locked achievement status</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — Achievement icons, community icon</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Submit report CTA</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="region"</code> with descriptive label.</li>
        <li>Achievement icons use <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden="true"</code> — status is communicated via text.</li>
        <li>Locked badges have visual opacity reduction plus text label for screen readers.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Messaging frames contributions as helpful to others rather than competitive.</li>
        <li>Locked badges are shown gently (reduced opacity) without creating pressure to "earn" them.</li>
        <li>"Every report helps responders reach those in need faster" connects action to impact.</li>
    </ul>
</section>
@endsection
