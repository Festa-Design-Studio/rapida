@extends('layouts.rapida-docs')

@section('title', 'Spacing & Layout — RAPIDA UI')

@section('docs-content')
<header class="mb-gap-component">
    <h1 class="text-h1 font-heading font-bold tracking-tight text-rapida-blue-900">Spacing & Layout</h1>
    <p class="text-body text-text-secondary mt-2">Consistent spacing reduces cognitive load. RAPIDA uses a 4px base grid with named tokens for every level of visual hierarchy.</p>
</header>

{{-- Spacing Scale --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Spacing Scale</h2>
    <p class="text-body-sm text-text-secondary mb-element">All spacing values derive from a 4px base unit. Named tokens map to Tailwind utility classes.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card space-y-3">
        @foreach([
            ['nano', '4px', 'w-1'],
            ['micro', '8px', 'w-2'],
            ['element', '12px', 'w-3'],
            ['inner', '16px', 'w-4'],
            ['padding-card', '20px', 'w-5'],
            ['gap-component', '24px', 'w-6'],
            ['gap-section', '32px', 'w-8'],
            ['section', '40px', 'w-10'],
            ['padding-page-x-mob', '16px', 'w-4'],
            ['padding-page-x-tab', '24px', 'w-6'],
            ['48px', '48px', 'w-12'],
            ['64px', '64px', 'w-16'],
            ['96px', '96px', 'w-24'],
        ] as [$name, $value, $widthClass])
            <div class="flex items-center gap-4">
                <span class="text-caption font-mono text-text-placeholder w-36 shrink-0">{{ $name }}</span>
                <span class="text-caption text-text-secondary w-12 shrink-0 text-right">{{ $value }}</span>
                <div class="h-4 {{ $widthClass }} bg-rapida-blue-300 rounded-sm shrink-0"></div>
            </div>
        @endforeach
    </div>
</section>

{{-- Touch Targets --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Touch Targets</h2>
    <p class="text-body-sm text-text-secondary mb-element">Minimum 48px for all interactive elements. Crisis users often have shaking hands, wet fingers, or broken screens.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-end gap-inner">
            <div class="flex flex-col items-center gap-2">
                <div class="h-12 w-12 bg-rapida-blue-100 border-2 border-rapida-blue-700 rounded-lg flex items-center justify-center">
                    <span class="text-caption font-mono text-rapida-blue-900">48</span>
                </div>
                <span class="text-caption text-text-secondary">Minimum</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="h-14 w-14 bg-rapida-blue-100 border-2 border-rapida-blue-700 rounded-lg flex items-center justify-center">
                    <span class="text-caption font-mono text-rapida-blue-900">56</span>
                </div>
                <span class="text-caption text-text-secondary">Comfortable</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <div class="h-16 w-16 bg-rapida-blue-100 border-2 border-rapida-blue-700 rounded-lg flex items-center justify-center">
                    <span class="text-caption font-mono text-rapida-blue-900">64</span>
                </div>
                <span class="text-caption text-text-secondary">Large / Primary</span>
            </div>
        </div>
    </div>
</section>

{{-- Border Radius --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Border Radius System</h2>
    <p class="text-body-sm text-text-secondary mb-element">Rounded corners signal safety and approachability.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="flex items-end gap-inner flex-wrap">
            @foreach([
                ['rounded-sm', '2px', 'Small — tags'],
                ['rounded', '4px', 'Default — inputs'],
                ['rounded-lg', '8px', 'Cards, buttons'],
                ['rounded-xl', '12px', 'Modals, upload zones'],
                ['rounded-full', '9999px', 'Badges, dots, avatars'],
            ] as [$radiusClass, $value, $usage])
                <div class="flex flex-col items-center gap-2">
                    <div class="h-16 w-16 bg-rapida-blue-100 border-2 border-rapida-blue-400 {{ $radiusClass }} flex items-center justify-center">
                        <span class="text-caption font-mono text-rapida-blue-800">{{ $value }}</span>
                    </div>
                    <span class="text-caption text-text-secondary text-center max-w-[80px]">{{ $usage }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Shadow System --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Shadow System</h2>
    <p class="text-body-sm text-text-secondary mb-element">Subtle shadows create depth hierarchy. Use sparingly in field conditions where screens may be dimmed.</p>

    <div class="rounded-lg bg-surface-form border border-grey-100 p-padding-card">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-inner">
            @foreach([
                ['shadow-xs', 'XS'],
                ['shadow-sm', 'SM'],
                ['shadow', 'Default'],
                ['shadow-md', 'MD'],
                ['shadow-lg', 'LG'],
            ] as [$shadowClass, $label])
                <div class="flex flex-col items-center gap-2">
                    <div class="h-20 w-full bg-white {{ $shadowClass }} rounded-lg border border-grey-100 flex items-center justify-center">
                        <span class="text-caption font-mono text-text-secondary">{{ $shadowClass }}</span>
                    </div>
                    <span class="text-caption text-text-secondary">{{ $label }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-inner pt-inner border-t border-grey-100">
            <p class="text-label font-medium text-text-secondary mb-element">Focus Shadow</p>
            <div class="h-12 w-48 bg-white rounded-lg border-2 border-rapida-blue-700 ring-2 ring-rapida-blue-700 ring-offset-2 flex items-center justify-center">
                <span class="text-caption font-mono text-text-secondary">ring-2 ring-rapida-blue-700</span>
            </div>
        </div>
    </div>
</section>

{{-- Motion Tokens --}}
<section class="mb-gap-section">
    <h2 class="text-h3 font-heading font-semibold mb-element">Motion Tokens</h2>
    <p class="text-body-sm text-text-secondary mb-element">All motion respects <code class="text-caption font-mono bg-slate-100 px-1 py-0.5 rounded">prefers-reduced-motion</code>. Keep transitions short — users are under time pressure.</p>

    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead>
                <tr class="border-b border-grey-100">
                    <th class="py-3 pr-4 font-medium text-text-secondary">Token</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Duration</th>
                    <th class="py-3 pr-4 font-medium text-text-secondary">Easing</th>
                    <th class="py-3 font-medium text-text-secondary">Usage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-grey-100">
                <tr><td class="py-3 pr-4 font-mono text-caption">duration-100</td><td class="py-3 pr-4">100ms</td><td class="py-3 pr-4">ease-out</td><td class="py-3">Button active press</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">duration-150</td><td class="py-3 pr-4">150ms</td><td class="py-3 pr-4">ease-in-out</td><td class="py-3">Color transitions, hover states</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">duration-200</td><td class="py-3 pr-4">200ms</td><td class="py-3 pr-4">ease-in-out</td><td class="py-3">Toggle switch, sidebar slide</td></tr>
                <tr><td class="py-3 pr-4 font-mono text-caption">duration-300</td><td class="py-3 pr-4">300ms</td><td class="py-3 pr-4">ease-out</td><td class="py-3">Progress bar fill, skeleton pulse</td></tr>
            </tbody>
        </table>
    </div>
</section>
@endsection
