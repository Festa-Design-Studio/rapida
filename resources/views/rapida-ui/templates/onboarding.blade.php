@extends('layouts.rapida-docs')

@section('title', 'Onboarding Template — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Language & Onboarding</h1>
    <p class="text-body text-text-secondary mt-2">The app opens. Trust is established in 10 seconds. A person in crisis picks their language, reads one line, taps Continue.</p>
</header>

{{-- Live route --}}
<div class="mb-gap-component">
    <a href="{{ route('onboarding') }}" class="inline-flex items-center gap-2 text-body font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors duration-150">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        View live: /onboarding
    </a>
</div>

{{-- Components used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Organisms & Molecules Used</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.language-switcher</code> — 6 UN languages with RTL support</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Primary Continue + Ghost "How does this work?"</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — RAPIDA logo at top</li>
    </ul>
</section>

{{-- Preview 1 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Mobile (centered)</h2>
    <div class="rounded-lg border border-grey-100 bg-gradient-to-b from-rapida-blue-50 to-slate-50 p-8 text-center max-w-sm mx-auto">
        <div class="space-y-4">
            <x-atoms.icon name="rapida-logo" size="xl" class="text-rapida-blue-900 mx-auto" />
            <p class="text-h2 font-heading font-semibold text-slate-900">Report damage in your community</p>
            <x-molecules.language-switcher
                current="en"
                :languages="['en' => 'English', 'fr' => 'Francais', 'es' => 'Espanol', 'ar' => 'العربية', 'ru' => 'Русский', 'zh' => '中文']"
            />
            <x-atoms.button variant="primary" size="lg" class="w-full">Continue</x-atoms.button>
        </div>
    </div>
</section>

{{-- Preview 2 --}}
<section class="mb-gap-section">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Language selected (Arabic)</h2>
    <div class="rounded-lg border border-grey-100 bg-gradient-to-b from-rapida-blue-50 to-slate-50 p-8 text-center max-w-sm mx-auto">
        <div class="space-y-4">
            <x-atoms.icon name="rapida-logo" size="xl" class="text-rapida-blue-900 mx-auto" />
            <p class="text-h2 font-heading font-semibold text-slate-900">Report damage in your community</p>
            <x-molecules.language-switcher
                current="ar"
                :languages="['en' => 'English', 'fr' => 'Francais', 'es' => 'Espanol', 'ar' => 'العربية', 'ru' => 'Русский', 'zh' => '中文']"
            />
            <x-atoms.button variant="primary" size="lg" class="w-full">Continue</x-atoms.button>
        </div>
    </div>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Calm gradient background (rapida-blue-50 to slate-50) avoids visual aggression.</li>
        <li>Single welcome message — no walls of text for someone under stress.</li>
        <li>Language badges use 48px min touch targets for one-handed mobile use.</li>
        <li>No account creation, no sign-up wall. Instant access.</li>
        <li>"How does this work?" is a ghost button — present but not pressuring.</li>
    </ul>
</section>
@endsection
