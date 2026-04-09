@extends('layouts.rapida-docs')

@section('title', 'Pitch Video — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Pitch Video</h1>
    <p class="text-body text-text-secondary mt-2">Production guide for the UNDP Challenge submission video. Not a template in the app — a specification for the team to follow when recording.</p>
</header>

{{-- Production specs --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Production Specifications</h2>
    <div class="rounded-xl border border-slate-200 bg-white p-5 divide-y divide-slate-100">
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Platform</span>
            <span class="text-slate-900">Vimeo (unlisted link)</span>
        </div>
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Aspect Ratio</span>
            <span class="text-slate-900">16:9</span>
        </div>
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Max Duration</span>
            <span class="text-slate-900">3 minutes 30 seconds</span>
        </div>
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Resolution</span>
            <span class="text-slate-900">1920 x 1080 (1080p minimum)</span>
        </div>
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Captions</span>
            <span class="font-medium text-rapida-blue-900">Required (burned-in or Vimeo CC)</span>
        </div>
        <div class="py-3 flex justify-between text-body-sm">
            <span class="text-slate-500">Audio</span>
            <span class="text-slate-900">Clear narration, no background music required</span>
        </div>
    </div>
</section>

{{-- Story structure --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Suggested Story Structure</h2>
    <div class="space-y-4">
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-2 mb-2">
                <x-atoms.badge variant="info">0:00 - 0:30</x-atoms.badge>
                <span class="text-body font-medium text-slate-900">The Problem</span>
            </div>
            <p class="text-body-sm text-slate-600">A crisis hits. Communities have information. Responders cannot reach them. The gap between damage and response costs lives.</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-2 mb-2">
                <x-atoms.badge variant="info">0:30 - 1:30</x-atoms.badge>
                <span class="text-body font-medium text-slate-900">The Solution</span>
            </div>
            <p class="text-body-sm text-slate-600">RAPIDA: a PWA that works offline, in 6 languages, on any phone. Show the onboarding, submission wizard, and confirmation flow live.</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-2 mb-2">
                <x-atoms.badge variant="info">1:30 - 2:30</x-atoms.badge>
                <span class="text-body font-medium text-slate-900">The Impact</span>
            </div>
            <p class="text-body-sm text-slate-600">Show the coordinator dashboard, live map, and data export. Community data flows to responders in real time.</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-2 mb-2">
                <x-atoms.badge variant="info">2:30 - 3:30</x-atoms.badge>
                <span class="text-body font-medium text-slate-900">The Team & Vision</span>
            </div>
            <p class="text-body-sm text-slate-600">Festa Design Studio. Trauma-informed design. Scaling to any crisis, any language, any community.</p>
        </div>
    </div>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Key screens to capture</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach(['Onboarding', 'Map Home', 'Submission Wizard', 'Confirmation', 'My Reports', 'Report Detail', 'Dashboard', 'Data Export'] as $screen)
                <div class="rounded-lg bg-slate-100 border border-slate-200 p-3 text-center">
                    <p class="text-caption font-medium text-slate-700">{{ $screen }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Caption style guide</h2>
    <div class="rounded-lg border border-grey-100 bg-slate-900 p-padding-card text-center">
        <p class="text-body text-white font-medium">RAPIDA helps communities report damage</p>
        <p class="text-caption text-slate-400 mt-2">White text on dark background. Inter font. Sentence case.</p>
    </div>
</section>

{{-- Notes --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Production Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Captions are required for accessibility and for judges reviewing without audio.</li>
        <li>Screen recordings should use a real mobile device or emulator at 375px width.</li>
        <li>Use the live app routes for screen capture — not static mockups.</li>
        <li>Keep narration calm and clear. No hype language. Let the product speak.</li>
        <li>End with the RAPIDA logo and Festa Design Studio name.</li>
    </ul>
</section>
@endsection
