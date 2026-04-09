@props([
    'communityCount' => 0,
    'userReportCount' => 0,
    'badges' => [],
    'crisisId' => null,
    'h3CellId' => null,
])

@php
    $defaultBadges = count($badges) > 0 ? $badges : [
        ['name' => 'First Report', 'icon' => 'star', 'earned' => true],
        ['name' => '5 Reports', 'icon' => 'shield', 'earned' => false],
        ['name' => 'Verified Reporter', 'icon' => 'check-circle', 'earned' => false],
    ];

    $progressService = app(\App\Services\ProgressRingService::class);
    $coverage = $crisisId ? $progressService->coverage($crisisId, $h3CellId) : ['total' => 0, 'reported' => 0, 'percentage' => 0];
    $leaderboard = $crisisId ? $progressService->leaderboard($crisisId, 5) : [];
@endphp

<div
    {{ $attributes->class(['w-full rounded-xl border border-slate-200 bg-white p-6 space-y-6']) }}
    role="region"
    aria-label="{{ __('rapida.community_contributions') }}"
>
    {{-- Header --}}
    <div class="text-center space-y-2">
        <x-atoms.icon name="community" size="xl" class="text-rapida-blue-700 mx-auto" />
        <h2 class="text-h3 font-heading font-semibold text-slate-900">{{ __('rapida.community_contributions') }}</h2>
        <p class="text-body text-text-secondary">{{ __('rapida.community_contributions_desc') }}</p>
    </div>

    {{-- Community stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 p-4 text-center">
            <p class="text-display font-heading font-bold text-rapida-blue-900">{{ $communityCount }}</p>
            <p class="text-body-sm text-rapida-blue-700">{{ __('rapida.community_count_label') }}</p>
        </div>
        <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-center">
            <p class="text-display font-heading font-bold text-text-primary">{{ $userReportCount }}</p>
            <p class="text-body-sm text-text-secondary">{{ __('rapida.user_count_label') }}</p>
        </div>
    </div>

    {{-- Progress ring — zone coverage --}}
    @if($coverage['total'] > 0)
        <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-4">
            <div class="flex items-center gap-4">
                {{-- SVG ring --}}
                <div class="relative shrink-0" style="width: 64px; height: 64px;">
                    <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#b8d9c8" stroke-width="3" />
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#2a5540" stroke-width="3"
                                stroke-dasharray="{{ $coverage['percentage'] }}, 100"
                                stroke-linecap="round" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-caption font-semibold text-ground-green-900">
                        {{ $coverage['percentage'] }}%
                    </span>
                </div>
                <div>
                    <p class="text-body-sm font-medium text-ground-green-900">{{ __('rapida.progress_ring_title') }}</p>
                    <p class="text-caption text-ground-green-700">
                        {{ __('rapida.progress_ring_buildings', ['reported' => $coverage['reported'], 'total' => $coverage['total']]) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Leaderboard — top contributors --}}
    @if(count($leaderboard) > 0)
        <div>
            <h3 class="text-h4 font-heading font-semibold text-text-primary mb-3">{{ __('rapida.leaderboard_title') }}</h3>
            <div class="space-y-2">
                @foreach($leaderboard as $entry)
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-2
                                {{ $entry['rank'] <= 3 ? 'bg-rapida-blue-50 border-rapida-blue-100' : 'bg-white' }}">
                        <span class="text-body-sm font-bold text-rapida-blue-700 w-6 text-center">{{ $entry['rank'] }}</span>
                        <span class="flex-1 text-body-sm text-text-primary">
                            {{ substr($entry['account_id'], 0, 8) }}...
                        </span>
                        <x-atoms.badge variant="info">
                            {{ __('rapida.leaderboard_reports', ['count' => $entry['report_count']]) }}
                        </x-atoms.badge>
                    </div>
                @endforeach
            </div>
            <p class="text-caption text-text-placeholder mt-2">{{ __('rapida.leaderboard_anonymous') }}</p>
        </div>
    @endif

    {{-- Earned badges --}}
    <div>
        <h3 class="text-h4 font-heading font-semibold text-text-primary mb-3">{{ __('rapida.achievements') }}</h3>
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
                {{ __('wizard.btn_submit_another') }}
            </x-atoms.button>
        </a>
    </div>
</div>
