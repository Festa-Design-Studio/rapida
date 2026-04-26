@extends('layouts.rapida-docs')

@section('title', 'Icon — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Icon</h1>
    <p class="text-body text-text-secondary mt-2">Inline SVG icons loaded from <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">resources/svg/icons/</code>. Falls back to a warning triangle when the icon file is not found. Color is inherited via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">currentColor</code>.</p>
</header>

{{-- Preview 1: All 6 Sizes --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — All 6 Sizes</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-end gap-inner">
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="xs" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">xs (12px)</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="sm" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">sm (16px)</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="md" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">md (20px)</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">lg (24px)</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="xl" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">xl (40px)</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="2xl" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">2xl (64px)</span>
            </div>
        </div>
    </div>
</div>

{{-- Preview 2: Navigation Icons --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Navigation</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            @foreach(['home', 'menu', 'back', 'close', 'chevron-down', 'chevron-right', 'external-link'] as $icon)
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon :name="$icon" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">{{ $icon }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview 3: Map Icons --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Map</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            @foreach(['pin', 'target', 'layers', 'compass', 'cluster'] as $icon)
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon :name="$icon" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">{{ $icon }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview 4: Damage / Crisis Icons --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Damage / Crisis</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            @foreach(['warning', 'building', 'road', 'bridge', 'power', 'water', 'flood', 'earthquake', 'fire', 'conflict'] as $icon)
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon :name="$icon" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">{{ $icon }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview 5: Status Icons --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Status</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            @foreach(['check-circle', 'x-circle', 'info', 'cloud-offline', 'cloud-upload', 'badge-verified', 'history'] as $icon)
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon :name="$icon" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">{{ $icon }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview 6: Action Icons --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Actions</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            @foreach(['edit', 'trash', 'share', 'download', 'filter', 'search', 'camera', 'language', 'shield-exit'] as $icon)
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon :name="$icon" size="lg" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">{{ $icon }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Preview 7: Color Variations --}}
<div class="mb-gap-component">
    <h2 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Preview — Color Variations</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-center gap-inner">
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="check-circle" size="lg" class="text-rapida-blue-700" />
                <span class="text-caption text-text-placeholder">rapida-blue-700</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="info" size="lg" class="text-slate-600" />
                <span class="text-caption text-text-placeholder">slate-600</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="warning" size="lg" class="text-amber-500" />
                <span class="text-caption text-text-placeholder">amber-500</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="pin" size="lg" class="text-green-600" />
                <span class="text-caption text-text-placeholder">green-600</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="cloud-upload" size="lg" class="text-rapida-blue-700" />
                <span class="text-caption text-text-placeholder">rapida-blue-700</span>
            </div>
        </div>
    </div>
</div>

{{-- Variants --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Variants</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-start">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pe-4 font-medium text-text-secondary">Size</th>
                        <th class="py-3 pe-4 font-medium text-text-secondary">Dimensions</th>
                        <th class="py-3 font-medium text-text-secondary">Usage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr><td class="py-3 pe-4 font-mono text-caption">xs</td><td class="py-3 pe-4">12 x 12px</td><td class="py-3">Inline with caption text, badge icons</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">sm</td><td class="py-3 pe-4">16 x 16px</td><td class="py-3">Inside buttons, form input icons</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">md</td><td class="py-3 pe-4">20 x 20px</td><td class="py-3">Default — navigation, list items</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">lg</td><td class="py-3 pe-4">24 x 24px</td><td class="py-3">Card headers, empty states</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">xl</td><td class="py-3 pe-4">40 x 40px</td><td class="py-3">Hero illustrations, upload zone</td></tr>
                    <tr><td class="py-3 pe-4 font-mono text-caption">2xl</td><td class="py-3 pe-4">64 x 64px</td><td class="py-3">Full-page empty states, onboarding</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">States</h2>
    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-center gap-inner">
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="home" size="md" class="text-slate-700" />
                <span class="text-caption text-text-placeholder">SVG file found</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-atoms.icon name="nonexistent-icon" size="md" class="text-slate-400" />
                <span class="text-caption text-text-placeholder">Fallback (missing)</span>
            </div>
        </div>
    </div>
</section>

{{-- Accessibility --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Accessibility</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>All icons are decorative by default: <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-hidden="true"</code>.</li>
        <li>Icons used as the sole content of a button must have an <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-label</code> on the parent button.</li>
        <li>Inline SVGs inherit color from the parent via <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">currentColor</code> stroke, controlled with Tailwind <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">text-*</code> classes.</li>
        <li>SVGs have no hardcoded width/height — they fill their container span via CSS.</li>
    </ul>
</section>

{{-- Trauma-informed --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Trauma-Informed Design</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Icons always accompany text — they reinforce meaning but never carry it alone.</li>
        <li>Fallback SVG ensures the UI never shows a broken image, even offline.</li>
        <li>Icon sizes are generous to maintain clarity on cracked or dirty screens.</li>
        <li>The icon set avoids graphic depictions of violence or injury.</li>
    </ul>
</section>
@endsection
