@extends('layouts.rapida-docs')

@section('title', 'Navigation Header — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Navigation Header</h1>
    <p class="text-body text-text-secondary mt-2">Global navigation bar with Safe Exit button, language switcher, and online status indicator. Always visible at the top of every screen. The Safe Exit button is a trauma-informed feature that immediately navigates away from the app.</p>
</header>

{{-- Preview 1 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Default State</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 overflow-hidden">
        <x-organisms.navigation-header />
    </div>
</div>

{{-- Preview 2 --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Active Route (Report)</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 overflow-hidden">
        <x-organisms.navigation-header currentRoute="report" />
    </div>
</div>

{{-- Molecules used --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Molecules & Atoms Composed</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-molecules.language-switcher</code> — Language selection</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.button</code> — Safe Exit button (safe-exit variant)</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.icon</code> — Brand logo</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">x-atoms.badge</code> — Online/offline status indicator</li>
    </ul>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">role="banner"</code> semantic landmark.</li>
        <li>Navigation uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label="Main navigation"</code>.</li>
        <li>Active link uses <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-current="page"</code>.</li>
        <li>Safe Exit has descriptive aria-label explaining its purpose.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Safe Exit button is always visible — allows users to quickly leave in unsafe situations.</li>
        <li>Navigates to a neutral external site (Google) to avoid leaving evidence of app usage.</li>
        <li>Subtle gradient background provides calm without distraction.</li>
        <li>Online status badge gives reassurance that data will be transmitted.</li>
    </ul>
</section>
@endsection
