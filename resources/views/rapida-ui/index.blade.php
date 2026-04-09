@extends('layouts.rapida-docs')

@section('title', 'RAPIDA UI · Design System')

@section('docs-content')
<header class="mb-gap-section">
    <h1 class="text-display font-heading font-bold tracking-tight text-rapida-blue-900">RAPIDA UI</h1>
    <p class="text-body-lg text-text-secondary mt-2">Trauma-Informed Interface Design System for Crisis-Context Digital Tools</p>
</header>

{{-- Story --}}
<div class="bg-surface-card rounded-lg p-padding-card shadow-sm mb-gap-section border border-grey-100">
    <p class="text-body-lg text-text-primary italic">
        A flood hits. A road collapses. A bridge is gone.<br>
        A community member opens RAPIDA. Picks their language. Drops a pin. Submits a report.<br>
        A UNDP coordinator sees it — live — on a map.<br>
        <span class="font-medium not-italic">That's the whole story. Every component below serves it.</span>
    </p>
</div>

{{-- Tokens --}}
<section class="mb-gap-section">
    <h2 class="text-h2 font-heading font-semibold text-rapida-blue-900 mb-gap-component">Design Tokens</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-inner">
        <a href="{{ route('rapida-ui.tokens.colors') }}" class="block bg-surface-card rounded-lg p-padding-card border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
            <div class="flex gap-micro mb-micro">
                <span class="h-4 w-4 rounded-full bg-rapida-blue-900"></span>
                <span class="h-4 w-4 rounded-full bg-ground-green-800"></span>
                <span class="h-4 w-4 rounded-full bg-alert-amber-500"></span>
                <span class="h-4 w-4 rounded-full bg-crisis-rose-400"></span>
            </div>
            <h3 class="text-h4 font-heading font-medium">Colors</h3>
            <p class="text-body-sm text-text-secondary mt-nano">Palette, semantic mappings, gradients, WCAG</p>
        </a>
        <a href="{{ route('rapida-ui.tokens.typography') }}" class="block bg-surface-card rounded-lg p-padding-card border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
            <p class="text-h3 font-heading font-semibold mb-micro">Aa</p>
            <h3 class="text-h4 font-heading font-medium">Typography</h3>
            <p class="text-body-sm text-text-secondary mt-nano">Inter + Noto Sans, type scale, RTL</p>
        </a>
        <a href="{{ route('rapida-ui.tokens.spacing') }}" class="block bg-surface-card rounded-lg p-padding-card border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
            <div class="flex gap-nano mb-micro">
                <div class="h-4 w-2 bg-rapida-blue-300 rounded-sm"></div>
                <div class="h-4 w-4 bg-rapida-blue-300 rounded-sm"></div>
                <div class="h-4 w-8 bg-rapida-blue-300 rounded-sm"></div>
            </div>
            <h3 class="text-h4 font-heading font-medium">Spacing & Layout</h3>
            <p class="text-body-sm text-text-secondary mt-nano">8px scale, touch targets, grid, shadows, motion</p>
        </a>
        <a href="{{ route('rapida-ui.tokens.states') }}" class="block bg-surface-card rounded-lg p-padding-card border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
            <div class="flex gap-nano mb-micro">
                <span class="h-4 w-4 rounded-full bg-rapida-blue-700"></span>
                <span class="h-4 w-4 rounded-full bg-green-500"></span>
                <span class="h-4 w-4 rounded-full bg-amber-500"></span>
                <span class="h-4 w-4 rounded-full bg-red-400"></span>
            </div>
            <h3 class="text-h4 font-heading font-medium">States</h3>
            <p class="text-body-sm text-text-secondary mt-nano">Interaction, validation, sync, damage levels</p>
        </a>
    </div>
</section>

{{-- Atoms --}}
<section class="mb-gap-section">
    <h2 class="text-h2 font-heading font-semibold text-rapida-blue-900 mb-gap-component">Atoms</h2>
    <p class="text-body text-text-secondary mb-gap-component">The smallest building blocks. Every molecule, organism, and template is assembled from these.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-inner">
        @foreach([
            ['button', 'Button', 'Submit, navigate, confirm'],
            ['text-input', 'Text Input', 'Address, description, search'],
            ['textarea', 'Textarea', 'Damage description'],
            ['photo-upload', 'Photo Upload', 'Evidence capture'],
            ['select', 'Select', 'Dropdowns, filters'],
            ['radio-group', 'Radio Group', 'Damage classification'],
            ['checkbox', 'Checkbox', 'Multi-select, confirmation'],
            ['toggle', 'Toggle', 'Layer visibility, settings'],
            ['icon', 'Icon', 'Noun Project icons'],
            ['badge', 'Badge', 'Status, damage, sync'],
            ['progress-step', 'Progress Step', 'Wizard tracker'],
            ['loader', 'Loader', 'Spinners, skeletons'],
        ] as [$slug, $name, $desc])
            <a href="{{ route("rapida-ui.atoms.{$slug}") }}" class="block bg-surface-card rounded-lg p-inner border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
                <h3 class="text-body font-medium">{{ $name }}</h3>
                <p class="text-caption text-text-placeholder mt-nano">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</section>

{{-- Molecules --}}
<section class="mb-gap-section">
    <h2 class="text-h2 font-heading font-semibold text-rapida-blue-900 mb-gap-component">Molecules</h2>
    <p class="text-body text-text-secondary mb-gap-component">Atoms assembled with purpose. Each molecule solves one specific reporting or navigation problem.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-inner">
        @foreach([
            ['damage-report-card', 'Damage Report Card', 'One submitted report at a glance'],
            ['form-field-group', 'Form Field Group', 'Label + input + validation'],
            ['language-switcher', 'Language Switcher', '6 UN languages + RTL'],
            ['offline-queue', 'Offline Queue', 'Sync status indicator'],
            ['damage-classification', 'Damage Classification', 'The 3-option critical tap'],
            ['infrastructure-type', 'Infrastructure Type', 'Road, bridge, power, water'],
            ['crisis-type', 'Crisis Type', 'Flood, earthquake, conflict'],
            ['map-pin', 'Map Pin', 'Damage-colored map markers'],
            ['notification', 'Notification', 'Alerts, offline, sync failures'],
            ['submission-confirmation', 'Submission Confirmation', 'The dignity moment'],
        ] as [$slug, $name, $desc])
            <a href="{{ route("rapida-ui.molecules.{$slug}") }}" class="block bg-surface-card rounded-lg p-inner border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
                <h3 class="text-body font-medium">{{ $name }}</h3>
                <p class="text-caption text-text-placeholder mt-nano">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</section>
{{-- Organisms --}}
<section class="mb-gap-section">
    <h2 class="text-h2 font-heading font-semibold text-rapida-blue-900 mb-gap-component">Organisms</h2>
    <p class="text-body text-text-secondary mb-gap-component">Full interface sections. Each one owns a complete user task.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-inner">
        @foreach([
            ['submission-wizard', 'Submission Wizard', '5-step report submission'],
            ['map-organism', 'Map Organism', 'Live damage map view'],
            ['navigation-header', 'Navigation Header', 'Global nav + Safe Exit'],
            ['community-report-feed', 'Community Report Feed', 'Live submission feed'],
            ['analytics-dashboard', 'Analytics Dashboard', 'UNDP coordinator KPIs'],
            ['data-export', 'Data Export', 'CSV / GeoJSON / PDF'],
            ['engagement-panel', 'Engagement Panel', 'Community recognition'],
            ['report-version-history', 'Report Version History', 'Edit timeline'],
        ] as [$slug, $name, $desc])
            <a href="{{ route("rapida-ui.organisms.{$slug}") }}" class="block bg-surface-card rounded-lg p-inner border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
                <h3 class="text-body font-medium">{{ $name }}</h3>
                <p class="text-caption text-text-placeholder mt-nano">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</section>
{{-- Templates --}}
<section class="mb-gap-section">
    <h2 class="text-h2 font-heading font-semibold text-rapida-blue-900 mb-gap-component">Templates</h2>
    <p class="text-body text-text-secondary mb-gap-component">Full screens. Each template is one moment in the reporter's or coordinator's journey.</p>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-inner">
        @foreach([
            ['onboarding', 'Language & Onboarding', 'Trust in 10 seconds'],
            ['map-home', 'Map Home', 'Living crisis map'],
            ['submission-wizard', 'Submission Wizard', '5-step report flow'],
            ['submission-confirmation', 'Confirmation', 'The dignity moment'],
            ['my-reports', 'My Reports', 'Your data, always yours'],
            ['report-detail', 'Report Detail', 'Full context, correctable'],
            ['analytics-dashboard', 'UNDP Dashboard', 'Real-time crisis view'],
            ['data-export', 'Data Export', 'CSV / GeoJSON / PDF'],
            ['pitch-video', 'Pitch Video', 'Production guide'],
        ] as [$slug, $name, $desc])
            <a href="{{ route("rapida-ui.templates.{$slug}") }}" class="block bg-surface-card rounded-lg p-inner border border-grey-100 hover:border-rapida-blue-300 hover:shadow-sm transition-all duration-150">
                <h3 class="text-body font-medium">{{ $name }}</h3>
                <p class="text-caption text-text-placeholder mt-nano">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</section>

{{-- Footer --}}
<footer class="pt-gap-section border-t border-grey-100">
    <p class="text-caption text-text-placeholder">RAPIDA UI by Festa Design Studio. Every component designed for a person in crisis.</p>
</footer>
@endsection
