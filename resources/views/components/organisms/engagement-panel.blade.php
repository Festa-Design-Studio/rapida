@props([
    'communityCount' => 0,
    'userReportCount' => 0,
    'badges' => [],
])

@php
    $defaultBadges = count($badges) > 0 ? $badges : [
        ['name' => 'First Report', 'icon' => 'star', 'earned' => true],
        ['name' => '5 Reports', 'icon' => 'shield', 'earned' => false],
        ['name' => 'Verified Reporter', 'icon' => 'check-circle', 'earned' => false],
    ];
@endphp

<div
    {{ $attributes->class(['w-full rounded-xl border border-slate-200 bg-white p-6 space-y-6']) }}
    role="region"
    aria-label="Community engagement and recognition"
>
    {{-- Header --}}
    <div class="text-center space-y-2">
        <x-atoms.icon name="community" size="xl" class="text-rapida-blue-700 mx-auto" />
        <h2 class="text-h3 font-heading font-semibold text-slate-900">{{ __('rapida.community_contributions') }}</h2>
        <p class="text-body text-slate-600">{{ __('rapida.community_contributions_desc') }}</p>
    </div>

    {{-- Community stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 p-4 text-center">
            <p class="text-display font-heading font-bold text-rapida-blue-900">{{ $communityCount }}</p>
            <p class="text-body-sm text-rapida-blue-700">{{ __('rapida.community_count_label') }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-center">
            <p class="text-display font-heading font-bold text-slate-900">{{ $userReportCount }}</p>
            <p class="text-body-sm text-slate-600">{{ __('rapida.user_count_label') }}</p>
        </div>
    </div>

    {{-- Earned badges --}}
    <div>
        <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">{{ __('rapida.achievements') }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($defaultBadges as $badge)
                <div class="flex items-center gap-3 rounded-lg border p-4
                            {{ $badge['earned'] ? 'border-rapida-blue-100 bg-rapida-blue-50' : 'border-slate-200 bg-slate-50 opacity-60' }}">
                    <x-atoms.icon
                        name="{{ $badge['icon'] }}"
                        size="md"
                        class="{{ $badge['earned'] ? 'text-rapida-blue-700' : 'text-slate-400' }}"
                    />
                    <div>
                        <p class="text-body-sm font-medium {{ $badge['earned'] ? 'text-rapida-blue-900' : 'text-slate-500' }}">
                            {{ $badge['name'] }}
                        </p>
                        @if($badge['earned'])
                            <x-atoms.badge variant="verified" size="default">{{ __('rapida.earned') }}</x-atoms.badge>
                        @else
                            <x-atoms.badge variant="draft" size="default">{{ __('rapida.locked') }}</x-atoms.badge>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="text-center pt-2">
        <a href="{{ route('submit') }}">
            <x-atoms.button variant="primary" size="md">
                Submit Another Report
            </x-atoms.button>
        </a>
    </div>
</div>
