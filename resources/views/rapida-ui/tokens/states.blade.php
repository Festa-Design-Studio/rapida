@extends('layouts.rapida-docs')

@section('title', 'States — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">States</h1>
    <p class="text-body text-text-secondary mt-2">Every interactive element in RAPIDA has clearly defined states. In crisis contexts, a user must always know: What can I do? What just happened? What is the system doing?</p>
</header>

{{-- Interaction States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Interaction States</h2>
    <p class="text-body-sm text-text-secondary mb-element">All interactive elements cycle through these states. Each must be visually distinct.</p>

    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pr-4 font-medium text-text-secondary">State</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Visual Treatment</th>
                    <th class="py-3 font-medium text-text-secondary">Example</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr>
                    <td class="py-3 pr-4 font-medium">Default</td>
                    <td class="py-3 pr-4">Base styling with clear affordance</td>
                    <td class="py-3"><x-atoms.button variant="primary">Submit</x-atoms.button></td>
                </tr>
                <tr>
                    <td class="py-3 pr-4 font-medium">Hover</td>
                    <td class="py-3 pr-4">Lightened or darkened background (150ms transition)</td>
                    <td class="py-3 text-caption text-text-placeholder italic">Hover over the button above</td>
                </tr>
                <tr>
                    <td class="py-3 pr-4 font-medium">Focus</td>
                    <td class="py-3 pr-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">ring-2 ring-rapida-blue-700 ring-offset-2</code></td>
                    <td class="py-3 text-caption text-text-placeholder italic">Tab to the button above</td>
                </tr>
                <tr>
                    <td class="py-3 pr-4 font-medium">Active / Pressed</td>
                    <td class="py-3 pr-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">scale-[0.98]</code> micro-scale</td>
                    <td class="py-3 text-caption text-text-placeholder italic">Click and hold</td>
                </tr>
                <tr>
                    <td class="py-3 pr-4 font-medium">Disabled</td>
                    <td class="py-3 pr-4"><code class="font-mono bg-slate-100 px-1 py-0.5 rounded">opacity-40 cursor-not-allowed</code></td>
                    <td class="py-3"><x-atoms.button variant="primary" :disabled="true">Disabled</x-atoms.button></td>
                </tr>
                <tr>
                    <td class="py-3 pr-4 font-medium">Loading</td>
                    <td class="py-3 pr-4">Spinner icon + disabled + <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">aria-live="polite"</code></td>
                    <td class="py-3"><x-atoms.button variant="primary" :loading="true">Submitting...</x-atoms.button></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Validation States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Validation States</h2>
    <p class="text-body-sm text-text-secondary mb-element">Validation errors must be clear and non-punishing. Use <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">role="alert"</code> for screen reader announcements.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-gap-component">
        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Default — No Errors</h3>
            <x-atoms.text-input name="state-default" label="Location name" placeholder="e.g. Main bridge, Sector 4" />
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Error State</h3>
            <x-atoms.text-input name="state-error" label="Location name" error="Please enter the infrastructure name." />
        </div>

        <div>
            <h3 class="text-label font-medium text-text-secondary uppercase tracking-widest mb-micro">Success / Valid</h3>
            <p class="text-body-sm text-text-secondary">No explicit success state on inputs — absence of error is confirmation. Use badges or toasts for submission success.</p>
        </div>
    </div>
</section>

{{-- Data / Sync States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Data & Sync States</h2>
    <p class="text-body-sm text-text-secondary mb-element">Reports move through sync states. Field workers often have intermittent connectivity.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner">
            <x-atoms.badge variant="draft">Draft</x-atoms.badge>
            <x-atoms.badge variant="synced">Synced</x-atoms.badge>
            <x-atoms.badge variant="pending">Pending sync</x-atoms.badge>
            <x-atoms.badge variant="failed">Sync failed</x-atoms.badge>
            <x-atoms.badge variant="verified">Verified</x-atoms.badge>
        </div>

        <div class="mt-inner pt-inner border-t border-grey-100">
            <div class="overflow-x-auto">
                <table class="w-full text-body-sm text-left">
                    <thead>
                        <tr class="border-b border-grey-100">
                            <th class="py-3 pr-4 font-medium text-text-secondary">State</th>
                            <th class="py-3 pr-4 font-medium text-text-secondary">Color</th>
                            <th class="py-3 font-medium text-text-secondary">Meaning</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        <tr><td class="py-3 pr-4">Draft</td><td class="py-3 pr-4">Slate</td><td class="py-3">Report saved locally, not submitted</td></tr>
                        <tr><td class="py-3 pr-4">Synced</td><td class="py-3 pr-4">Green</td><td class="py-3">Successfully uploaded to server</td></tr>
                        <tr><td class="py-3 pr-4">Pending</td><td class="py-3 pr-4">Amber</td><td class="py-3">Queued for upload, waiting for connection</td></tr>
                        <tr><td class="py-3 pr-4">Failed</td><td class="py-3 pr-4">Red</td><td class="py-3">Upload failed, will retry automatically</td></tr>
                        <tr><td class="py-3 pr-4">Verified</td><td class="py-3 pr-4">RAPIDA Blue</td><td class="py-3">Coordinator has reviewed and confirmed</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Damage Level States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Damage Level States</h2>
    <p class="text-body-sm text-text-secondary mb-element">Damage classification uses a three-level scale with color-coded badges and radio cards.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex flex-wrap items-center gap-inner mb-inner">
            <x-atoms.badge variant="minimal">Minimal</x-atoms.badge>
            <x-atoms.badge variant="partial">Partial</x-atoms.badge>
            <x-atoms.badge variant="complete">Complete</x-atoms.badge>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead>
                    <tr class="border-b border-grey-100">
                        <th class="py-3 pr-4 font-medium text-text-secondary">Level</th>
                        <th class="py-3 pr-4 font-medium text-text-secondary">Color</th>
                        <th class="py-3 pr-4 font-medium text-text-secondary">Dot</th>
                        <th class="py-3 font-medium text-text-secondary">Meaning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grey-100">
                    <tr>
                        <td class="py-3 pr-4">Minimal</td>
                        <td class="py-3 pr-4">Green</td>
                        <td class="py-3 pr-4"><span class="inline-block h-3 w-3 rounded-full bg-green-500"></span></td>
                        <td class="py-3">Infrastructure usable, cosmetic damage only</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-4">Partial</td>
                        <td class="py-3 pr-4">Amber</td>
                        <td class="py-3 pr-4"><span class="inline-block h-3 w-3 rounded-full bg-amber-500"></span></td>
                        <td class="py-3">Significant damage, limited functionality</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-4">Complete</td>
                        <td class="py-3 pr-4">Red</td>
                        <td class="py-3 pr-4"><span class="inline-block h-3 w-3 rounded-full bg-red-600"></span></td>
                        <td class="py-3">Fully destroyed or impassable</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Loading States --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Loading States</h2>
    <p class="text-body-sm text-text-secondary mb-element">Always show loading feedback. Never leave the user guessing whether the app is working.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-inner">
            <div class="flex flex-col items-center gap-3 p-inner">
                <x-atoms.loader variant="spinner" size="md" />
                <span class="text-caption text-text-secondary">Inline spinner</span>
            </div>
            <div class="flex flex-col items-center gap-3 p-inner">
                <x-atoms.loader variant="skeleton-text" />
                <span class="text-caption text-text-secondary">Skeleton text</span>
            </div>
            <div class="flex flex-col items-center gap-3 p-inner">
                <x-atoms.loader variant="progress-ring" :percentage="45" />
                <span class="text-caption text-text-secondary">Progress ring</span>
            </div>
        </div>
    </div>
</section>

{{-- Reduced Motion --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Reduced Motion</h2>
    <ul class="list-disc list-inside space-y-nano text-body-sm text-text-secondary">
        <li>All animated components use <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">motion-reduce:</code> variants.</li>
        <li>Spinners switch to <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">animate-pulse</code> when reduced motion is preferred.</li>
        <li>Skeleton loaders stop pulsing and show static placeholder blocks.</li>
        <li>Transitions reduce to 0ms — state changes are instant.</li>
        <li>Progress bars still show position but do not animate the fill transition.</li>
    </ul>
</section>
@endsection
