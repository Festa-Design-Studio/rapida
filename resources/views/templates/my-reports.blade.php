@extends('layouts.rapida')

@section('title', 'My Reports — RAPIDA')

@section('content')
@php
    $reportCards = $reports->map(fn ($r) => [
        'id' => $r->id,
        'photo' => $r->photo_url,
        'damageLevel' => $r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level,
        'infrastructureType' => $r->infrastructure_type,
        'location' => $r->landmark_text ?? ($r->latitude ? "{$r->latitude}, {$r->longitude}" : 'Location pending'),
        'description' => $r->description ?? '',
        'reporterName' => 'You',
        'submittedAt' => $r->submitted_at?->format('M d, Y H:i') ?? '',
        'syncStatus' => $r->synced_at ? 'synced' : 'pending',
    ])->all();

    $syncedCount = $reports->filter(fn ($r) => $r->synced_at !== null)->count();
    $pendingCount = $reports->filter(fn ($r) => $r->synced_at === null)->count();
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header />

    {{-- Content --}}
    <main class="flex-1 px-4 md:px-6 py-8 max-w-3xl mx-auto w-full">
        <header class="mb-6">
            <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('rapida.my_reports') }}</h1>
            <p class="text-body text-slate-600 mt-1">{{ __('rapida.my_reports_desc') }}</p>
        </header>

        {{-- Sync summary --}}
        @if($reports->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-6">
                @if($syncedCount > 0)
                    <x-atoms.badge variant="synced">{{ $syncedCount }} {{ __('rapida.synced') }}</x-atoms.badge>
                @endif
                @if($pendingCount > 0)
                    <x-atoms.badge variant="pending">{{ $pendingCount }} {{ __('rapida.pending') }}</x-atoms.badge>
                @endif
            </div>
        @endif

        {{-- Report feed --}}
        <x-organisms.community-report-feed
            :reports="$reportCards"
            :emptyMessage="__('rapida.no_reports_yet')"
        />

        {{-- CTA --}}
        <div class="mt-8 text-center">
            <a href="{{ route('submit') }}">
                <x-atoms.button variant="primary" size="lg">
                    {{ __('rapida.submit_new_report') }}
                </x-atoms.button>
            </a>
        </div>
    </main>
</div>
@endsection
