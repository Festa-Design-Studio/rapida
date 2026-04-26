@extends('layouts.rapida-docs')

@section('title', 'Colors — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Color System</h1>
    <p class="text-body text-text-secondary mt-2">Every color is a psychological safety decision. No red on primary surfaces. No pure black. No bright yellow. Colors must signal safety, ground the user, and communicate urgency without panic.</p>
</header>

{{-- Rapida Blue --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Rapida Blue — Trust Anchor</h2>
    <p class="text-body-sm text-text-secondary mb-element">Deep, desaturated blue. Primary brand, navigation, headers, focus rings.</p>
    <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
        @foreach([
            ['950', 'bg-rapida-blue-950', '#0f2330'],
            ['900', 'bg-rapida-blue-900', '#1a3a4a'],
            ['800', 'bg-rapida-blue-800', '#24506a'],
            ['700', 'bg-rapida-blue-700', '#2e6689'],
            ['500', 'bg-rapida-blue-500', '#4a8db5'],
            ['300', 'bg-rapida-blue-300', '#8dbdd8'],
            ['100', 'bg-rapida-blue-100', '#d0e8f2'],
            ['50',  'bg-rapida-blue-50',  '#f0f7fa'],
        ] as [$shade, $class, $hex])
            <div class="flex flex-col items-center gap-1">
                <div class="h-14 w-full rounded-lg {{ $class }} border border-black/5"></div>
                <span class="text-caption font-medium">{{ $shade }}</span>
                <span class="text-caption text-text-placeholder font-mono">{{ $hex }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Ground Green --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Ground Green — Stability & Success</h2>
    <p class="text-body-sm text-text-secondary mb-element">Muted forest green. Success, confirmation, submitted states only — never decorative.</p>
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
        @foreach([
            ['900', 'bg-ground-green-900', '#1e3d2f'],
            ['800', 'bg-ground-green-800', '#2a5540'],
            ['700', 'bg-ground-green-700', '#366d52'],
            ['500', 'bg-ground-green-500', '#518c6d'],
            ['200', 'bg-ground-green-200', '#b8d9c8'],
            ['50',  'bg-ground-green-50',  '#f0f9f4'],
        ] as [$shade, $class, $hex])
            <div class="flex flex-col items-center gap-1">
                <div class="h-14 w-full rounded-lg {{ $class }} border border-black/5"></div>
                <span class="text-caption font-medium">{{ $shade }}</span>
                <span class="text-caption text-text-placeholder font-mono">{{ $hex }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Alert Amber --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Alert Amber — Caution Without Alarm</h2>
    <p class="text-body-sm text-text-secondary mb-element">Warm amber. Partial damage classification, offline warnings. Never for errors.</p>
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
        @foreach([
            ['900', 'bg-alert-amber-900', '#7a4510'],
            ['700', 'bg-alert-amber-700', '#a35e18'],
            ['500', 'bg-alert-amber-500', '#c47d2a'],
            ['300', 'bg-alert-amber-300', '#dba96a'],
            ['100', 'bg-alert-amber-100', '#f5e4c8'],
            ['50',  'bg-alert-amber-50',  '#fdf6ec'],
        ] as [$shade, $class, $hex])
            <div class="flex flex-col items-center gap-1">
                <div class="h-14 w-full rounded-lg {{ $class }} border border-black/5"></div>
                <span class="text-caption font-medium">{{ $shade }}</span>
                <span class="text-caption text-text-placeholder font-mono">{{ $hex }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Crisis Rose --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Crisis Rose — Calm Urgency</h2>
    <p class="text-body-sm text-text-secondary mb-element">Desaturated warm coral. Complete damage classification. This is not red — it must never become red.</p>
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
        @foreach([
            ['900', 'bg-crisis-rose-900', '#5c2420'],
            ['700', 'bg-crisis-rose-700', '#8c3d38'],
            ['400', 'bg-crisis-rose-400', '#c46b5a'],
            ['300', 'bg-crisis-rose-300', '#d4917f'],
            ['100', 'bg-crisis-rose-100', '#f2dbd6'],
            ['50',  'bg-crisis-rose-50',  '#fdf3f1'],
        ] as [$shade, $class, $hex])
            <div class="flex flex-col items-center gap-1">
                <div class="h-14 w-full rounded-lg {{ $class }} border border-black/5"></div>
                <span class="text-caption font-medium">{{ $shade }}</span>
                <span class="text-caption text-text-placeholder font-mono">{{ $hex }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Neutrals --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Neutrals</h2>
    <p class="text-body-sm text-text-secondary mb-element">Near-black through off-white. Body text, borders, backgrounds, disabled states.</p>
    <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
        @foreach([
            ['n-900', 'bg-neutral-900', '#1a1a1a'],
            ['g-900', 'bg-grey-900', '#333333'],
            ['g-700', 'bg-grey-700', '#555555'],
            ['g-500', 'bg-grey-500', '#888888'],
            ['g-300', 'bg-grey-300', '#cccccc'],
            ['g-100', 'bg-grey-100', '#eeeeee'],
            ['n-50',  'bg-neutral-50', '#f7f7f5'],
        ] as [$shade, $class, $hex])
            <div class="flex flex-col items-center gap-1">
                <div class="h-14 w-full rounded-lg {{ $class }} border border-black/5"></div>
                <span class="text-caption font-medium">{{ $shade }}</span>
                <span class="text-caption text-text-placeholder font-mono">{{ $hex }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- Semantic Color Mapping --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Semantic Color Mapping</h2>
    <p class="text-body-sm text-text-secondary mb-element">All code references semantic tokens — never raw hex. One change updates every surface.</p>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-start">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pe-4 font-medium text-text-secondary">Token</th>
                    <th class="py-3 pe-4 font-medium text-text-secondary">Role</th>
                    <th class="py-3 pe-4 font-medium text-text-secondary">Source</th>
                    <th class="py-3 font-medium text-text-secondary">Swatch</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pe-4 font-mono text-caption">surface-page</td><td class="py-3 pe-4">Page backgrounds</td><td class="py-3 pe-4">rapida-blue-50</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-surface-page border border-black/5"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">surface-form</td><td class="py-3 pe-4">Form backgrounds</td><td class="py-3 pe-4">neutral-50</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-surface-form border border-black/5"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">surface-nav</td><td class="py-3 pe-4">Navigation bar</td><td class="py-3 pe-4">rapida-blue-900</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-surface-nav"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">text-primary</td><td class="py-3 pe-4">All body text</td><td class="py-3 pe-4">grey-900</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-text-primary"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">text-secondary</td><td class="py-3 pe-4">Labels, helper text</td><td class="py-3 pe-4">grey-700</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-text-secondary"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">color-primary</td><td class="py-3 pe-4">Brand, interactive</td><td class="py-3 pe-4">rapida-blue-900</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-color-primary"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">color-success</td><td class="py-3 pe-4">Success states</td><td class="py-3 pe-4">ground-green-900</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-color-success"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">color-warning</td><td class="py-3 pe-4">Warning states</td><td class="py-3 pe-4">alert-amber-500</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-color-warning"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">color-critical</td><td class="py-3 pe-4">Critical states</td><td class="py-3 pe-4">crisis-rose-400</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-color-critical"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">damage-minimal</td><td class="py-3 pe-4">No/minimal damage</td><td class="py-3 pe-4">ground-green-800</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-damage-minimal"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">damage-partial</td><td class="py-3 pe-4">Partial damage</td><td class="py-3 pe-4">alert-amber-500</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-damage-partial"></span></td></tr>
                <tr><td class="py-3 pe-4 font-mono text-caption">damage-complete</td><td class="py-3 pe-4">Complete damage</td><td class="py-3 pe-4">crisis-rose-400</td><td class="py-3"><span class="inline-block h-5 w-10 rounded bg-damage-complete"></span></td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Calm Gradients --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Calm Gradients</h2>
    <p class="text-body-sm text-text-secondary mb-element">Subtle, directional, perceptually smooth. Max 2 stops, 160-180 degrees. Never on interactive elements.</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-inner">
        <div class="flex flex-col gap-nano">
            <div class="h-24 rounded-lg gradient-trust-wash"></div>
            <span class="text-label font-medium">Trust Wash</span>
            <span class="text-caption text-text-placeholder">Nav / header</span>
        </div>
        <div class="flex flex-col gap-nano">
            <div class="h-24 rounded-lg gradient-ground-calm"></div>
            <span class="text-label font-medium">Ground Calm</span>
            <span class="text-caption text-text-placeholder">Confirmation / success</span>
        </div>
        <div class="flex flex-col gap-nano">
            <div class="h-24 rounded-lg gradient-selenite-rest border border-grey-100"></div>
            <span class="text-label font-medium">Selenite Rest</span>
            <span class="text-caption text-text-placeholder">Form / wizard card</span>
        </div>
        <div class="flex flex-col gap-nano">
            <div class="h-24 rounded-lg gradient-dawn-warmth"></div>
            <span class="text-label font-medium">Dawn Warmth</span>
            <span class="text-caption text-text-placeholder">Dignity screen</span>
        </div>
        <div class="flex flex-col gap-nano">
            <div class="h-24 rounded-lg gradient-map-overlay"></div>
            <span class="text-label font-medium">Map Overlay</span>
            <span class="text-caption text-text-placeholder">Translucent panel</span>
        </div>
    </div>
</section>

{{-- Damage Classification --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Damage Classification</h2>
    <p class="text-body-sm text-text-secondary mb-element">The 3-level system used on badges, map pins, report cards, and the confirmation screen.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-inner">
        <div class="rounded-lg p-padding-card bg-damage-minimal-surface border border-ground-green-200">
            <div class="flex items-center gap-micro mb-micro">
                <span class="h-3 w-3 rounded-full bg-ground-green-800"></span>
                <span class="text-label font-medium text-damage-minimal">Minimal / No Damage</span>
            </div>
            <p class="text-caption text-text-secondary">Structurally sound, cosmetic or no visible damage</p>
        </div>
        <div class="rounded-lg p-padding-card bg-damage-partial-surface border border-alert-amber-300">
            <div class="flex items-center gap-micro mb-micro">
                <span class="h-3 w-3 rounded-full bg-alert-amber-500"></span>
                <span class="text-label font-medium text-damage-partial">Partially Damaged</span>
            </div>
            <p class="text-caption text-text-secondary">Repairable, remains usable with caution</p>
        </div>
        <div class="rounded-lg p-padding-card bg-damage-complete-surface border border-crisis-rose-300">
            <div class="flex items-center gap-micro mb-micro">
                <span class="h-3 w-3 rounded-full bg-crisis-rose-400"></span>
                <span class="text-label font-medium text-damage-complete">Completely Damaged</span>
            </div>
            <p class="text-caption text-text-secondary">Structurally unsafe or destroyed</p>
        </div>
    </div>
</section>

{{-- WCAG Contrast --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">WCAG Contrast Ratios</h2>
    <p class="text-body-sm text-text-secondary mb-element">All text/background combinations verified against WCAG 2.1 AA (4.5:1 normal, 3:1 large text).</p>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-start">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pe-4 font-medium text-text-secondary">Combination</th>
                    <th class="py-3 pe-4 font-medium text-text-secondary">Ratio</th>
                    <th class="py-3 font-medium text-text-secondary">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pe-4">grey-900 on neutral-50</td><td class="py-3 pe-4">12.6:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">grey-900 on white</td><td class="py-3 pe-4">13.9:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">rapida-blue-50 on rapida-blue-900</td><td class="py-3 pe-4">9.8:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">ground-green-900 on ground-green-50</td><td class="py-3 pe-4">8.4:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">white on ground-green-800</td><td class="py-3 pe-4">5.1:1</td><td class="py-3 font-medium text-ground-green-700">AA</td></tr>
                <tr><td class="py-3 pe-4">alert-amber-900 on alert-amber-50</td><td class="py-3 pe-4">9.1:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">white on alert-amber-500</td><td class="py-3 pe-4">3.2:1</td><td class="py-3 font-medium text-alert-amber-700">AA Large</td></tr>
                <tr><td class="py-3 pe-4">crisis-rose-900 on crisis-rose-50</td><td class="py-3 pe-4">10.2:1</td><td class="py-3 font-medium text-ground-green-700">AAA</td></tr>
                <tr><td class="py-3 pe-4">white on crisis-rose-400</td><td class="py-3 pe-4">3.6:1</td><td class="py-3 font-medium text-alert-amber-700">AA Large</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Absolute Rules --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Absolute Color Rules</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li><strong>Never use red (#ff0000 or similar saturated reds)</strong> — triggers physiological stress in trauma-sensitised users</li>
        <li><strong>Never use pure black (#000000)</strong> — harsh contrast increases eye strain on low-quality screens</li>
        <li><strong>Never use bright yellow for warnings</strong> — high-saturation yellow is anxiety-inducing at scale</li>
        <li><strong>Crisis Rose must never be made more saturated</strong> — increased saturation shifts it toward red</li>
        <li><strong>Ground Green is for success/submitted states only</strong> — decorative use dilutes the success signal</li>
        <li><strong>Alert Amber is for functional states only</strong> — do not use as decorative accent</li>
        <li><strong>All page backgrounds must use -50 scale tokens</strong> — pure white feels clinical; off-whites feel safe</li>
    </ul>
</section>
@endsection
