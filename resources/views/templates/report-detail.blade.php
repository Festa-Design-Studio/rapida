@extends('layouts.rapida')

@section('title', 'Report Detail — RAPIDA')

@section('content')
@php
    $damageLevel = $report->damage_level instanceof \App\Enums\DamageLevel ? $report->damage_level->value : ($report->damage_level ?? 'partial');
    $damageLevelLabel = match($damageLevel) {
        'minimal'  => __('rapida.damage_minimal'),
        'partial'  => __('rapida.damage_partial'),
        'complete' => __('rapida.damage_complete'),
        default    => ucfirst($damageLevel),
    };
    $syncStatus = $report->synced_at ? 'synced' : 'pending';
    $location = $report->landmark_text ?? ($report->latitude ? "{$report->latitude}, {$report->longitude}" : 'Location pending');

    $versions = [];
    if ($report->verification) {
        $versions[] = [
            'version' => 2,
            'status' => 'current',
            'editedBy' => 'Verification Officer',
            'editedAt' => $report->verification->created_at?->format('Y-m-d H:i') ?? '',
            'changes' => 'Verified by UNDP staff.',
        ];
    }
    $versions[] = [
        'version' => 1,
        'status' => $report->verification ? 'original' : 'current',
        'editedBy' => 'Community Reporter',
        'editedAt' => $report->submitted_at?->format('Y-m-d H:i') ?? '',
        'changes' => 'Initial report submitted.',
    ];
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header />

    <main class="flex-1 px-4 md:px-6 py-8 max-w-3xl mx-auto w-full space-y-8">
        {{-- Photo --}}
        <div class="w-full h-64 rounded-xl bg-slate-200 flex items-center justify-center border border-slate-200 overflow-hidden">
            @if($report->photo_url)
                <img src="{{ str_starts_with($report->photo_url, 'http') ? $report->photo_url : Storage::url($report->photo_url) }}" alt="Damage photo" class="w-full h-full object-cover" />
            @else
                <div class="text-center space-y-2">
                    <x-atoms.icon name="camera" size="xl" class="text-slate-400 mx-auto" />
                    <p class="text-body-sm text-slate-500">Damage photo</p>
                </div>
            @endif
        </div>

        {{-- Header info --}}
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <x-atoms.badge variant="{{ $damageLevel }}">{{ $damageLevelLabel }}</x-atoms.badge>
                <x-atoms.badge variant="{{ $syncStatus }}">{{ ucfirst($syncStatus) }}</x-atoms.badge>
            </div>

            <h1 class="text-h1 font-heading font-bold text-slate-900">{{ $location }}</h1>

            <p class="text-body text-slate-600">{{ $report->description ?? '' }}</p>
        </div>

        {{-- Metadata --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 divide-y divide-slate-100">
            <div class="py-3 flex justify-between text-body-sm">
                <span class="text-slate-500">{{ __('rapida.report_id') }}</span>
                <span class="font-mono text-slate-900">{{ substr($report->id, 0, 8) }}</span>
            </div>
            <div class="py-3 flex justify-between text-body-sm">
                <span class="text-slate-500">{{ __('rapida.infrastructure') }}</span>
                <span class="text-slate-900">{{ ucfirst($report->infrastructure_type ?? '') }}</span>
            </div>
            <div class="py-3 flex justify-between text-body-sm">
                <span class="text-slate-500">{{ __('rapida.crisis_type') }}</span>
                <span class="text-slate-900">{{ ucfirst($report->crisis_type ?? '') }}</span>
            </div>
            <div class="py-3 flex justify-between text-body-sm">
                <span class="text-slate-500">{{ __('rapida.reported_by') }}</span>
                <span class="text-slate-900">{{ __('rapida.community_reporter') }}</span>
            </div>
            <div class="py-3 flex justify-between text-body-sm">
                <span class="text-slate-500">{{ __('rapida.submitted') }}</span>
                <time datetime="{{ $report->submitted_at?->toIso8601String() }}" class="text-slate-900">{{ $report->submitted_at?->format('M d, Y H:i') ?? '' }}</time>
            </div>
        </div>

        {{-- Location map — zoomed to street level, single pin --}}
        @if($report->latitude && $report->longitude)
            <div>
                <h2 class="text-h4 font-heading font-semibold text-slate-900 mb-3">{{ __('rapida.location') }}</h2>
                <x-organisms.map-report
                    :latitude="$report->latitude"
                    :longitude="$report->longitude"
                    :damageLevel="$damageLevel"
                    :label="$location"
                    height="h-56"
                    :zoom="17"
                />
            </div>
        @endif

        {{-- Version History --}}
        <x-organisms.report-version-history :versions="$versions" />

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('my-reports') }}">
                <x-atoms.button variant="ghost" size="md">
                    {{ __('rapida.back_to_reports') }}
                </x-atoms.button>
            </a>
        </div>
    </main>
</div>
@endsection
