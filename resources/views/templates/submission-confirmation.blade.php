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

    // Gap-49: real impact counter scoped to the just-submitted report's H3 cell
    // and (for logged-in accounts) the reporter's submission history. Falls
    // back to zero when no report context is available — e.g., a direct visit
    // without a ?report= query param.
    $analytics = app(\App\Services\AnalyticsQueryService::class);
    $communityCount = $report
        ? $analytics->reportsInH3Cell((string) $report->crisis_id, $report->h3_cell_id)
        : 0;
    $userReportCount = $report && $report->account_id
        ? $analytics->reportsByAccount((string) $report->crisis_id, (string) $report->account_id)
        : 0;
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

        {{-- Engagement Panel — gap-49 wiring --}}
        <x-organisms.engagement-panel
            :communityCount="$communityCount"
            :userReportCount="$userReportCount"
        />
    </main>
</div>
@endsection
