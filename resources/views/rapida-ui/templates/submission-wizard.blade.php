@extends('layouts.rapida-docs')

@section('title', 'Submission Wizard Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Submission Wizard</h1>
    <p class="text-body text-text-secondary mt-2">5 steps. Works offline. Never loses a draft. Guides a community member through photo, location, damage classification, details, and review.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('submit') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /submit
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.navigation-header</code> — With back button context</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-organisms.submission-wizard</code> — Full 5-step wizard flow</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Step 1 (Photo capture)</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.submission-wizard :currentStep="1" />
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Step 5 (Review before submit)</h2>
    <div class="rounded-lg border border-grey-100 bg-surface-form p-padding-card">
        <x-organisms.submission-wizard :currentStep="5" />
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>One task per screen — reduces cognitive load for someone under stress.</li>
        <li>"Submit what you have" ghost link on every step. Partial reports are valued.</li>
        <li>Back button always visible — users never feel trapped in a flow.</li>
        <li>Progress dots show completion — a sense of control and progress.</li>
        <li>Designed for offline-first. Drafts are saved locally and sync when connectivity returns.</li>
    </ul>
</section>
@endsection
