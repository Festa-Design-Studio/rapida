@extends('layouts.rapida')

@section('title', 'RAPIDA — Crisis Damage Map')

@section('content')
@php
    $reportCards = $reports->map(fn ($r) => [
        'id' => $r->id,
        // Photo is gated: surface only on cards an analyst has verified.
        // The molecule's existing fallback icon renders when $photo is null.
        // Trauma-informed: never publicly surface unmoderated user photos
        // that may include faces, license plates, or identifying landmarks.
        // verification.status is cast to VerificationStatus enum, so we
        // compare values rather than strict-equal an enum to a string.
        'photo' => $r->verification?->status?->value === 'verified' ? $r->photo_url : null,
        'damageLevel' => $r->damage_level instanceof \App\Enums\DamageLevel
            ? $r->damage_level->value
            : $r->damage_level,
        'infrastructureType' => $r->infrastructure_type,
        'location' => $r->landmark_text
            ?? ($r->latitude
                ? number_format($r->latitude, 4) . ', ' . number_format($r->longitude, 4)
                : __('rapida.location_pending') ?? 'Location pending'),
        'description' => $r->description ?? '',
        'reporterName' => 'Community Reporter',
        'submittedAt' => $r->submitted_at?->format('M d, Y') ?? '',
        'syncStatus' => $r->synced_at ? 'synced' : 'pending',
        'crisisType' => $r->crisis_type ?? '',
    ])->all();
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header currentRoute="map" :crisis="$crisis" />

    {{-- Map (~60vh mobile / 70vh desktop). fullscreen=true removes the
         organism's default rounded-xl card chrome so the map sits flush
         with the section dividers above and below it. --}}
    <div class="relative">
        <x-organisms.map-organism
            height="h-[60vh] sm:h-[70vh]"
            :reports="$reportCards"
            :crisisSlug="$crisis?->slug"
            :centerLat="5.56"
            :centerLng="-0.20"
            :zoom="13"
            mode="reporter"
            :fullscreen="true"
        />
    </div>

    {{-- Submit a Report — full-width section, primary action --}}
    <section class="bg-white border-t border-slate-200 px-4 sm:px-6 py-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-h4 font-heading font-semibold text-slate-900 mb-3">
                {{ __('rapida.submit_a_report') }}
            </h2>
            <a
                href="{{ $crisis ? route('crisis.show', $crisis->slug) : route('onboarding') }}"
                class="block"
            >
                <x-atoms.button variant="primary" size="lg" class="w-full gap-3">
                    <x-atoms.icon name="camera" size="sm" />
                    <span>{{ __('rapida.report_damage') }}</span>
                </x-atoms.button>
            </a>
        </div>
    </section>

    {{-- Recent Reports — uses the molecule. Photo appears only when verified. --}}
    <section class="px-4 sm:px-6 py-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-h4 font-heading font-semibold text-slate-900 mb-3">
                {{ __('rapida.recent_reports') }}
                @if($reports->isNotEmpty())
                    <span class="text-body-sm font-normal text-slate-500">({{ $reports->count() }})</span>
                @endif
            </h2>

            @if($reports->isEmpty())
                <div class="text-center py-8">
                    <x-atoms.icon name="pin" size="lg" class="text-slate-300 mx-auto mb-2" />
                    <p class="text-body text-slate-500">{{ __('rapida.no_reports_community') }}</p>
                    <p class="text-body-sm text-slate-400 mt-1">{{ __('rapida.be_first') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($reportCards as $card)
                        <x-molecules.damage-report-card
                            :photo="$card['photo']"
                            :damageLevel="$card['damageLevel']"
                            :infrastructureType="$card['infrastructureType']"
                            :location="$card['location']"
                            :description="$card['description']"
                            :reporterName="$card['reporterName']"
                            :submittedAt="$card['submittedAt']"
                            :syncStatus="$card['syncStatus']"
                            :crisisType="$card['crisisType']"
                            :href="route('report-detail', $card['id'])"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
