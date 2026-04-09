@extends('layouts.rapida-docs')

@section('title', 'Language Switcher — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Language Switcher</h1>
    <p class="text-body text-text-secondary mt-2">Allows field reporters to switch between available languages. Composes Badge atoms as radio-style selectors. Critical for multilingual crisis contexts where reports must be submitted in the reporter's preferred language.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Three Languages</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.language-switcher
            current="en"
            :languages="['en' => 'English', 'ar' => 'Arabic', 'ne' => 'Nepali']"
        />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Arabic Selected</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <x-molecules.language-switcher
            current="ar"
            :languages="['en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish']"
        />
    </div>
</div>

{{-- All Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Configurations</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6">
        <div>
            <p class="text-caption text-text-placeholder mb-2">Two languages</p>
            <x-molecules.language-switcher
                current="en"
                :languages="['en' => 'English', 'ar' => 'Arabic']"
            />
        </div>
        <div>
            <p class="text-caption text-text-placeholder mb-2">Five languages</p>
            <x-molecules.language-switcher
                current="fr"
                :languages="['en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'es' => 'Spanish', 'ne' => 'Nepali']"
            />
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="radiogroup"</code> with descriptive <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code>.</li>
        <li>Each radio input has an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> with the full language name.</li>
        <li>Focus is visible via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">peer-focus-visible:ring-2</code> on the badge.</li>
        <li>Touch targets meet the 48px minimum for field use.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Language codes are universally recognizable — EN, AR, NE — reducing cognitive load.</li>
        <li>Active language uses high-contrast rapida-blue to clearly indicate the current selection.</li>
        <li>Switcher works offline — language change does not require a network request.</li>
        <li>Large touch targets accommodate stressed users in field conditions.</li>
    </ul>
</section>
@endsection
