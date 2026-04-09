@extends('layouts.rapida')

@section('title', 'Report Submitted — RAPIDA')

@section('content')
@php
    $reportData = $report ? [
        'reportId' => substr($report->id, 0, 8),
        'damageLevel' => $report->damage_level instanceof \App\Enums\DamageLevel ? $report->damage_level->value : ($report->damage_level ?? 'partial'),
        'syncStatus' => $report->synced_at ? 'synced' : 'pending',
        'submittedAt' => $report->submitted_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
    ] : [
        'reportId' => 'RPD-' . now()->format('Y'),
        'damageLevel' => 'partial',
        'syncStatus' => 'pending',
        'submittedAt' => now()->format('Y-m-d H:i'),
    ];
@endphp

<div class="min-h-screen flex flex-col bg-surface-page">
    {{-- Navigation Header --}}
    <x-organisms.navigation-header />

    {{-- Confirmation --}}
    <main class="flex-1 bg-green-50/30 px-4 py-12 flex flex-col items-center gap-8 max-w-2xl mx-auto w-full">
        <x-molecules.submission-confirmation
            :reportId="$reportData['reportId']"
            :damageLevel="$reportData['damageLevel']"
            :syncStatus="$reportData['syncStatus']"
            :submittedAt="$reportData['submittedAt']"
        />

        {{-- Engagement Panel --}}
        <x-organisms.engagement-panel
            :communityCount="142"
            :userReportCount="3"
        />
    </main>
</div>
@endsection
