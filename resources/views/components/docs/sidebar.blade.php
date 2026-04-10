@props(['current' => ''])

@php
    $sections = [
        'Tokens' => [
            ['label' => 'Colors', 'route' => 'rapida-ui.tokens.colors', 'key' => 'tokens.colors'],
            ['label' => 'Typography', 'route' => 'rapida-ui.tokens.typography', 'key' => 'tokens.typography'],
            ['label' => 'Spacing & Layout', 'route' => 'rapida-ui.tokens.spacing', 'key' => 'tokens.spacing'],
            ['label' => 'States', 'route' => 'rapida-ui.tokens.states', 'key' => 'tokens.states'],
            ['label' => 'Logo', 'route' => 'rapida-ui.tokens.logo', 'key' => 'tokens.logo'],
        ],
        'Atoms' => [
            ['label' => 'Button', 'route' => 'rapida-ui.atoms.button', 'key' => 'atoms.button'],
            ['label' => 'Text Input', 'route' => 'rapida-ui.atoms.text-input', 'key' => 'atoms.text-input'],
            ['label' => 'Textarea', 'route' => 'rapida-ui.atoms.textarea', 'key' => 'atoms.textarea'],
            ['label' => 'Photo Upload', 'route' => 'rapida-ui.atoms.photo-upload', 'key' => 'atoms.photo-upload'],
            ['label' => 'Select', 'route' => 'rapida-ui.atoms.select', 'key' => 'atoms.select'],
            ['label' => 'Radio Group', 'route' => 'rapida-ui.atoms.radio-group', 'key' => 'atoms.radio-group'],
            ['label' => 'Checkbox', 'route' => 'rapida-ui.atoms.checkbox', 'key' => 'atoms.checkbox'],
            ['label' => 'Toggle', 'route' => 'rapida-ui.atoms.toggle', 'key' => 'atoms.toggle'],
            ['label' => 'Icon', 'route' => 'rapida-ui.atoms.icon', 'key' => 'atoms.icon'],
            ['label' => 'Badge', 'route' => 'rapida-ui.atoms.badge', 'key' => 'atoms.badge'],
            ['label' => 'Progress Step', 'route' => 'rapida-ui.atoms.progress-step', 'key' => 'atoms.progress-step'],
            ['label' => 'Loader', 'route' => 'rapida-ui.atoms.loader', 'key' => 'atoms.loader'],
        ],
        'Molecules' => [
            ['label' => 'Damage Report Card', 'route' => 'rapida-ui.molecules.damage-report-card', 'key' => 'molecules.damage-report-card'],
            ['label' => 'Form Field Group', 'route' => 'rapida-ui.molecules.form-field-group', 'key' => 'molecules.form-field-group'],
            ['label' => 'Language Switcher', 'route' => 'rapida-ui.molecules.language-switcher', 'key' => 'molecules.language-switcher'],
            ['label' => 'Offline Queue', 'route' => 'rapida-ui.molecules.offline-queue', 'key' => 'molecules.offline-queue'],
            ['label' => 'Damage Classification', 'route' => 'rapida-ui.molecules.damage-classification', 'key' => 'molecules.damage-classification'],
            ['label' => 'Infrastructure Type', 'route' => 'rapida-ui.molecules.infrastructure-type', 'key' => 'molecules.infrastructure-type'],
            ['label' => 'Crisis Type', 'route' => 'rapida-ui.molecules.crisis-type', 'key' => 'molecules.crisis-type'],
            ['label' => 'Map Pin', 'route' => 'rapida-ui.molecules.map-pin', 'key' => 'molecules.map-pin'],
            ['label' => 'Notification', 'route' => 'rapida-ui.molecules.notification', 'key' => 'molecules.notification'],
            ['label' => 'Submission Confirmation', 'route' => 'rapida-ui.molecules.submission-confirmation', 'key' => 'molecules.submission-confirmation'],
        ],
        'Organisms' => [
            ['label' => 'Submission Wizard', 'route' => 'rapida-ui.organisms.submission-wizard', 'key' => 'organisms.submission-wizard'],
            ['label' => 'Map Organism', 'route' => 'rapida-ui.organisms.map-organism', 'key' => 'organisms.map-organism'],
            ['label' => 'Navigation Header', 'route' => 'rapida-ui.organisms.navigation-header', 'key' => 'organisms.navigation-header'],
            ['label' => 'Community Report Feed', 'route' => 'rapida-ui.organisms.community-report-feed', 'key' => 'organisms.community-report-feed'],
            ['label' => 'Analytics Dashboard', 'route' => 'rapida-ui.organisms.analytics-dashboard', 'key' => 'organisms.analytics-dashboard'],
            ['label' => 'Data Export', 'route' => 'rapida-ui.organisms.data-export', 'key' => 'organisms.data-export'],
            ['label' => 'Engagement Panel', 'route' => 'rapida-ui.organisms.engagement-panel', 'key' => 'organisms.engagement-panel'],
            ['label' => 'Report Version History', 'route' => 'rapida-ui.organisms.report-version-history', 'key' => 'organisms.report-version-history'],
        ],
        'Templates' => [
            ['label' => 'Onboarding', 'route' => 'rapida-ui.templates.onboarding', 'key' => 'templates.onboarding'],
            ['label' => 'Map Home', 'route' => 'rapida-ui.templates.map-home', 'key' => 'templates.map-home'],
            ['label' => 'Submission Wizard', 'route' => 'rapida-ui.templates.submission-wizard', 'key' => 'templates.submission-wizard'],
            ['label' => 'Confirmation', 'route' => 'rapida-ui.templates.submission-confirmation', 'key' => 'templates.submission-confirmation'],
            ['label' => 'My Reports', 'route' => 'rapida-ui.templates.my-reports', 'key' => 'templates.my-reports'],
            ['label' => 'Report Detail', 'route' => 'rapida-ui.templates.report-detail', 'key' => 'templates.report-detail'],
            ['label' => 'Analytics Dashboard', 'route' => 'rapida-ui.templates.analytics-dashboard', 'key' => 'templates.analytics-dashboard'],
            ['label' => 'Data Export', 'route' => 'rapida-ui.templates.data-export', 'key' => 'templates.data-export'],
            ['label' => 'Pitch Video', 'route' => 'rapida-ui.templates.pitch-video', 'key' => 'templates.pitch-video'],
        ],
    ];
@endphp

<nav aria-label="Design system navigation" class="space-y-element">
    {{-- Logo / Home --}}
    <a href="{{ route('rapida-ui.index') }}"
       class="block text-h4 font-heading font-semibold text-rapida-blue-900 hover:text-rapida-blue-700 transition-colors duration-150 pb-micro border-b border-grey-100">
        RAPIDA UI
    </a>

    @foreach($sections as $sectionName => $items)
        <div x-data="{ open: {{ count($items) > 0 && collect($items)->contains('key', $current) ? 'true' : (count($items) > 0 ? 'true' : 'false') }} }">
            <button
                @click="open = !open"
                class="flex items-center justify-between w-full text-label font-medium uppercase tracking-widest
                       {{ count($items) > 0 ? 'text-slate-700 hover:text-slate-900 cursor-pointer' : 'text-slate-400 cursor-default' }}"
                @if(count($items) === 0) disabled @endif
            >
                <span>{{ $sectionName }}</span>
                @if(count($items) > 0)
                    <svg class="h-4 w-4 transition-transform duration-150" x-bind:class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                @else
                    <span class="text-caption text-slate-300 normal-case tracking-normal">Soon</span>
                @endif
            </button>

            @if(count($items) > 0)
                <ul x-show="open" x-collapse class="mt-nano space-y-nano pl-1">
                    @foreach($items as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="block py-1.5 px-3 rounded-md text-body-sm transition-colors duration-150
                                      {{ $current === $item['key']
                                          ? 'bg-rapida-blue-100 text-rapida-blue-900 font-medium'
                                          : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
</nav>
