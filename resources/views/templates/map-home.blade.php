@extends('layouts.rapida')

@section('title', 'RAPIDA — Crisis Damage Map')

@section('content')
@php
    $reportCards = $reports->map(fn ($r) => [
        'id' => $r->id,
        'photo' => $r->photo_url,
        'damageLevel' => $r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level,
        'infrastructureType' => $r->infrastructure_type,
        'location' => $r->landmark_text ?? ($r->latitude ? number_format($r->latitude, 4) . ', ' . number_format($r->longitude, 4) : 'Location pending'),
        'description' => $r->description ?? '',
        'reporterName' => 'Community Reporter',
        'submittedAt' => $r->submitted_at?->format('M d, Y') ?? '',
        'syncStatus' => $r->synced_at ? 'synced' : 'pending',
        'crisisType' => $r->crisis_type ?? '',
    ])->all();
@endphp

<div x-data="{ feedOpen: true }" class="h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header currentRoute="map" :crisis="$crisis" />

    {{-- Map (fills remaining height) --}}
    <div class="flex-1 relative">
        <x-organisms.map-organism
            height="h-full"
            :reports="$reportCards"
            :crisisSlug="$crisis?->slug"
            :centerLat="5.56"
            :centerLng="-0.20"
            :zoom="13"
            mode="reporter"
            :fullscreen="true"
        />

        {{-- Floating Report Button (bottom-right, above map legend) --}}
        <div class="absolute right-4 bottom-4 z-20">
            <a href="{{ $crisis ? route('crisis.show', $crisis->slug) : route('onboarding') }}">
                <x-atoms.button variant="primary" size="lg" class="shadow-lg rounded-full px-8 gap-3">
                    <x-atoms.icon name="camera" size="sm" />
                    <span>Report Damage</span>
                </x-atoms.button>
            </a>
        </div>

    </div>

    {{-- Bottom sheet toggle (outside map container to avoid MapLibre event capture) --}}
    <button
        @click="feedOpen = !feedOpen"
        class="relative z-20 bg-white border-t border-slate-200 px-4 py-3 flex items-center justify-between shrink-0"
        :class="feedOpen ? 'rounded-t-2xl' : ''"
        aria-label="Toggle report feed"
    >
        <span class="text-h4 font-heading font-semibold text-slate-900">
            Recent Reports
            @if($reports->isNotEmpty())
                <span class="text-body-sm font-normal text-slate-500">({{ $reports->count() }})</span>
            @endif
        </span>
        <span class="transition-transform duration-200" :class="feedOpen ? 'rotate-180' : ''">
            <x-atoms.icon name="chevron-down" size="md" class="text-slate-400" />
        </span>
    </button>

    {{-- Community Report Feed (bottom sheet) --}}
    <div
        x-show="feedOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="bg-white max-h-80 overflow-y-auto px-4 pb-4 md:px-6"
    >
        @if($reports->isEmpty())
            <div class="text-center py-8">
                <x-atoms.icon name="pin" size="lg" class="text-slate-300 mx-auto mb-2" />
                <p class="text-body text-slate-500">No reports submitted yet.</p>
                <p class="text-body-sm text-slate-400 mt-1">Be the first to help your community.</p>
            </div>
        @else
            <div class="space-y-3 pt-2">
                @foreach($reportCards as $report)
                    <a href="{{ route('report-detail', $report['id']) }}" class="block">
                        <div class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 hover:border-rapida-blue-300 hover:bg-rapida-blue-50/30 transition-colors duration-150">
                            {{-- Damage indicator --}}
                            <div class="shrink-0 mt-1">
                                <x-atoms.badge variant="{{ $report['damageLevel'] }}" size="default">
                                    {{ ucfirst($report['damageLevel']) }}
                                </x-atoms.badge>
                            </div>
                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-body-sm font-medium text-slate-900 truncate">
                                    {{ $report['infrastructureType'] ? ucfirst(str_replace('_', ' ', $report['infrastructureType'])) : 'Infrastructure' }}
                                </p>
                                <p class="text-caption text-slate-500 truncate">{{ $report['location'] }}</p>
                                <p class="text-caption text-slate-400">{{ $report['submittedAt'] }}</p>
                            </div>
                            {{-- Sync status --}}
                            <div class="shrink-0">
                                <x-atoms.badge variant="{{ $report['syncStatus'] }}">
                                    {{ ucfirst($report['syncStatus']) }}
                                </x-atoms.badge>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
