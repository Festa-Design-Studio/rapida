@extends('layouts.rapida-docs')

@section('title', 'Typography — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Typography</h1>
    <p class="text-body text-text-secondary mt-2">RAPIDA uses Inter for headings and UI chrome, Noto Sans for body text. Both are selected for high legibility under stress and broad language/script support.</p>
</header>

{{-- Font Specimens --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Font Specimens</h2>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card mb-gap-component">
        <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-element">Inter — Headings & UI</h3>
        <p class="font-heading text-display tracking-tight text-slate-900 mb-element">The quick brown fox jumps over the lazy dog</p>
        <div class="flex flex-wrap gap-inner text-body-sm text-text-secondary font-heading">
            <span class="font-semibold">Semi-bold 600</span>
            <span class="font-bold">Bold 700</span>
            <span class="font-medium">Medium 500</span>
        </div>
    </div>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-element">Noto Sans — Body & Forms</h3>
        <p class="font-sans text-body-lg text-slate-900 mb-element">The quick brown fox jumps over the lazy dog</p>
        <p class="font-sans text-body text-slate-900 mb-element" dir="rtl" lang="ar">الثعلب البني السريع يقفز فوق الكلب الكسول</p>
        <div class="flex flex-wrap gap-inner text-body-sm text-text-secondary font-sans">
            <span>Regular 400</span>
            <span class="font-medium">Medium 500</span>
        </div>
    </div>
</section>

{{-- Full Type Scale --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Type Scale</h2>
    <p class="text-body-sm text-text-secondary mb-element">Mobile-first sizes. Tablet and desktop scales are progressively larger.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-element">
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-display — 30px / 36px</span>
            <p class="text-display font-heading font-bold tracking-tight text-slate-900">Display Heading</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-h1 — 24px / 32px</span>
            <p class="text-h1 font-heading font-bold tracking-tight text-slate-900">Heading 1</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-h2 — 20px / 28px</span>
            <p class="text-h2 font-heading font-semibold text-slate-900">Heading 2</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-h3 — 18px / 26px</span>
            <p class="text-h3 font-heading font-semibold text-slate-900">Heading 3</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-h4 — 16px / 24px</span>
            <p class="text-h4 font-heading font-medium text-slate-900">Heading 4</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-body-lg — 18px / 28px</span>
            <p class="text-body-lg font-sans text-slate-900">Body Large — introductions and emphasis</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-body — 16px / 24px</span>
            <p class="text-body font-sans text-slate-900">Body — default paragraph and form text</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-body-sm — 14px / 20px</span>
            <p class="text-body-sm font-sans text-slate-900">Body Small — help text, captions, metadata</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-caption — 12px / 16px</span>
            <p class="text-caption font-sans text-slate-900">Caption — timestamps, counters, labels</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-label — 13px / 16px</span>
            <p class="text-label font-medium text-slate-900">Label — form labels, section titles</p>
        </div>
        <div class="border-b border-grey-100 pb-element">
            <span class="text-caption font-mono text-text-placeholder">text-btn — 16px / 20px</span>
            <p class="text-btn font-heading font-semibold text-slate-900">Button Text</p>
        </div>
        <div>
            <span class="text-caption font-mono text-text-placeholder">text-btn-sm — 14px / 18px</span>
            <p class="text-btn-sm font-heading font-semibold text-slate-900">Small Button Text</p>
        </div>
    </div>
</section>

{{-- Responsive Scales --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Responsive Scales</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pr-4 font-medium text-text-secondary">Token</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Mobile (&lt;768px)</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Tablet (768px+)</th>
                    <th class="py-3 font-medium text-text-secondary">Desktop (1024px+)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pr-4 font-mono text-caption">text-display</td><td class="py-3 pr-4">30px</td><td class="py-3 pr-4">36px</td><td class="py-3">40px</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">text-h1</td><td class="py-3 pr-4">24px</td><td class="py-3 pr-4">28px</td><td class="py-3">32px</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">text-h2</td><td class="py-3 pr-4">20px</td><td class="py-3 pr-4">22px</td><td class="py-3">24px</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">text-h3</td><td class="py-3 pr-4">18px</td><td class="py-3 pr-4">18px</td><td class="py-3">20px</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">text-body</td><td class="py-3 pr-4">16px</td><td class="py-3 pr-4">16px</td><td class="py-3">16px</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Weight Usage --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Weight Usage Rules</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pr-4 font-medium text-text-secondary">Weight</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Value</th>
                    <th class="py-3 font-medium text-text-secondary">Usage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pr-4 font-bold">Bold</td><td class="py-3 pr-4">700</td><td class="py-3">Display, H1 headings only</td></tr>
                <tr><td class="py-3 pr-4 font-semibold">Semi-bold</td><td class="py-3 pr-4">600</td><td class="py-3">H2, H3, buttons, emphasis</td></tr>
                <tr><td class="py-3 pr-4 font-medium">Medium</td><td class="py-3 pr-4">500</td><td class="py-3">H4, labels, form field labels</td></tr>
                <tr><td class="py-3 pr-4">Regular</td><td class="py-3 pr-4">400</td><td class="py-3">Body text, input values, help text</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Line Height & Letter Spacing --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Line Height & Letter Spacing</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Headings: line-height 1.2-1.33, letter-spacing <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">tracking-tight (-0.025em)</code></li>
        <li>Body text: line-height 1.5 (24px at 16px), default letter-spacing</li>
        <li>Body-lg: line-height 1.56 (28px at 18px) for comfortable reading</li>
        <li>Captions: line-height 1.33 (16px at 12px)</li>
        <li>Labels: uppercase tracking uses <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">tracking-widest (0.1em)</code></li>
        <li>Buttons: line-height 1.25 for vertical centering</li>
    </ul>
</section>

{{-- RTL Notes --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">RTL & Multilingual Notes</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>Noto Sans was chosen for its broad Unicode coverage: Arabic, Bengali, Cyrillic, Devanagari, and more.</li>
        <li>Use <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">dir="rtl"</code> on the <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">&lt;html&gt;</code> element for Arabic, Urdu, etc.</li>
        <li>Use logical CSS properties (<code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">ps-</code>, <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">pe-</code>, <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">ms-</code>, <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">me-</code>) instead of <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">pl-</code>/<code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">pr-</code> where text direction changes layout.</li>
        <li>Test all form labels and buttons in RTL before shipping.</li>
        <li>Icons beside text should flip position in RTL — use <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">rtl:flex-row-reverse</code>.</li>
    </ul>
</section>
@endsection
