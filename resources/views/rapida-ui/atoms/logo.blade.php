@extends('layouts.rapida-docs')

@section('title', 'Logo — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Logo</h1>
    <p class="text-body text-text-secondary mt-2">The RAPIDA logomark and wordmark system. Inline SVG for zero HTTP requests — critical for low-bandwidth crisis zones.</p>
</header>

{{-- Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card text-center space-y-3">
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest">Mark Only</h3>
            <div class="flex justify-center">
                <x-atoms.logo size="xl" variant="mark" class="text-rapida-blue-700" />
            </div>
            <code class="text-caption font-mono bg-slate-100 px-2 py-1 rounded">variant="mark"</code>
        </div>

        <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card text-center space-y-3">
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest">Full</h3>
            <div class="flex justify-center">
                <x-atoms.logo size="xl" variant="full" class="text-rapida-blue-700" />
            </div>
            <code class="text-caption font-mono bg-slate-100 px-2 py-1 rounded">variant="full"</code>
        </div>

        <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card text-center space-y-3">
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest">Responsive</h3>
            <div class="flex justify-center">
                <x-atoms.logo size="lg" variant="responsive" class="text-rapida-blue-700" />
            </div>
            <code class="text-caption font-mono bg-slate-100 px-2 py-1 rounded">variant="responsive"</code>
            <p class="text-caption text-text-placeholder">Text hidden on mobile</p>
        </div>
    </div>
</section>

{{-- Size Scale --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Size Scale</h2>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-6">
        @foreach(['xs' => '20px — Inline, badges', 'sm' => '24px — Compact header', 'md' => '32px — Default header', 'lg' => '40px — Dashboard', 'xl' => '56px — Onboarding', '2xl' => '80px — Splash'] as $size => $desc)
            <div class="flex items-center gap-6 border-b border-grey-100 pb-4 last:border-0 last:pb-0">
                <div class="w-24 shrink-0">
                    <x-atoms.logo size="{{ $size }}" variant="mark" class="text-rapida-blue-700" />
                </div>
                <div>
                    <code class="text-caption font-mono text-rapida-blue-700">size="{{ $size }}"</code>
                    <p class="text-body-sm text-text-secondary mt-0.5">{{ $desc }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Color Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Color Usage</h2>
    <p class="text-body-sm text-text-secondary mb-element">The logo inherits <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">currentColor</code>. Apply color via the parent's text class.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-lg bg-white border border-grey-100 p-6 flex items-center justify-center gap-4">
            <x-atoms.logo size="lg" variant="full" class="text-rapida-blue-700" />
            <span class="text-caption text-text-placeholder">Light background</span>
        </div>
        <div class="rounded-lg bg-rapida-blue-900 p-6 flex items-center justify-center gap-4">
            <x-atoms.logo size="lg" variant="full" class="text-white" />
            <span class="text-caption text-rapida-blue-300">Dark background</span>
        </div>
        <div class="rounded-lg bg-surface-page border border-grey-100 p-6 flex items-center justify-center gap-4">
            <x-atoms.logo size="lg" variant="full" class="text-rapida-blue-900" />
            <span class="text-caption text-text-placeholder">Page background</span>
        </div>
        <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-6 flex items-center justify-center gap-4">
            <x-atoms.logo size="lg" variant="full" class="text-ground-green-900" />
            <span class="text-caption text-ground-green-700">Success context</span>
        </div>
    </div>
</section>

{{-- Favicon --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Favicon</h2>
    <p class="text-body-sm text-text-secondary mb-element">SVG favicon at <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">/icons/favicon.svg</code>. Uses rapida-blue-700 (#2e6689) baked in.</p>
    <div class="flex items-center gap-6">
        <img src="/icons/favicon.svg" alt="RAPIDA favicon" class="w-8 h-8" />
        <img src="/icons/favicon.svg" alt="RAPIDA favicon" class="w-6 h-6" />
        <img src="/icons/favicon.svg" alt="RAPIDA favicon" class="w-4 h-4" />
        <span class="text-caption text-text-placeholder">32px · 24px · 16px</span>
    </div>
</section>

{{-- Low-Bandwidth Note --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Low-Bandwidth Performance</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Inline SVG — zero HTTP requests, renders immediately with the HTML</li>
        <li>~500 bytes total — smaller than a single icon font glyph</li>
        <li><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">currentColor</code> inheritance — no separate color assets needed</li>
        <li>Vector — renders crisp at any resolution, any pixel density</li>
        <li>No JavaScript dependency — renders even if scripts fail to load</li>
    </ul>
</section>

{{-- Usage --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Usage</h2>
    <div class="rounded-lg bg-slate-900 p-6 text-body-sm font-mono text-slate-300 space-y-2 overflow-x-auto">
        <p class="text-slate-500">{{-- Header (responsive — text hidden on mobile) --}}</p>
        <p>&lt;x-atoms.logo size="md" variant="responsive" class="text-rapida-blue-700" /&gt;</p>
        <p class="text-slate-500 mt-4">{{-- Onboarding splash --}}</p>
        <p>&lt;x-atoms.logo size="2xl" variant="full" class="text-rapida-blue-900" /&gt;</p>
        <p class="text-slate-500 mt-4">{{-- Dark footer --}}</p>
        <p>&lt;x-atoms.logo size="lg" variant="full" class="text-white" /&gt;</p>
        <p class="text-slate-500 mt-4">{{-- Favicon (in layout head) --}}</p>
        <p>&lt;link rel="icon" type="image/svg+xml" href="/icons/favicon.svg"&gt;</p>
    </div>
</section>
@endsection
