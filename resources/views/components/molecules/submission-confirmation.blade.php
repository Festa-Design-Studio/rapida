@props([
    'reportId' => null,
    'submittedAt' => null,
    'damageLevel' => 'minimal',
    'syncStatus' => 'synced',
])

@php
    $damageLevelLabel = match($damageLevel) {
        'minimal'  => __('rapida.damage_minimal'),
        'partial'  => __('rapida.damage_partial'),
        'complete' => __('rapida.damage_complete'),
        default    => ucfirst($damageLevel),
    };

    $syncLabel = match($syncStatus) {
        'synced'  => __('rapida.sync_synced'),
        'pending' => __('rapida.sync_pending'),
        'failed'  => __('rapida.sync_failed'),
        default   => ucfirst($syncStatus),
    };
@endphp

<div
    {{ $attributes->class([
        'w-full rounded-xl bg-ground-green-50 border border-ground-green-200 p-8 flex flex-col items-center text-center gap-4',
    ]) }}
    role="status"
    aria-live="polite"
    aria-label="Report submitted successfully"
>
    <x-atoms.icon name="check-circle" size="xl" class="text-ground-green-700" />

    <div class="space-y-2" x-data="{ isOnline: $store.offlineQueue?.isOnline ?? true }">
        <h2 class="text-h3 font-heading font-semibold text-slate-900">
            <span x-show="isOnline">{{ __('rapida.confirmation_online') }}</span>
            <span x-show="!isOnline" x-cloak>{{ __('rapida.confirmation_offline') }}</span>
        </h2>
        <p class="text-body text-slate-600">{{ __('rapida.confirmation_thanks') }}</p>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-2">
        <x-atoms.badge variant="{{ $damageLevel }}">{{ $damageLevelLabel }}</x-atoms.badge>
        <x-atoms.badge variant="{{ $syncStatus }}">{{ $syncLabel }}</x-atoms.badge>
    </div>

    @if($reportId || $submittedAt)
        <div class="text-caption text-slate-500 space-y-1">
            @if($reportId)
                <p>{{ __('rapida.report_id_label') }} <span class="font-mono">{{ $reportId }}</span></p>
            @endif
            @if($submittedAt)
                <p>{{ __('rapida.submitted_label') }} <time datetime="{{ $submittedAt }}">{{ $submittedAt }}</time></p>
            @endif
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-center gap-3 mt-2">
        <a href="{{ route('submit') }}" class="w-full">
            <x-atoms.button variant="primary" class="w-full">Submit Another Report</x-atoms.button>
        </a>
        <a href="{{ $reportId ? route('report-detail', $reportId) : route('my-reports') }}" class="w-full">
            <x-atoms.button variant="ghost" class="w-full">View Report</x-atoms.button>
        </a>
    </div>
</div>
