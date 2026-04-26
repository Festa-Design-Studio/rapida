@props([
    'stats' => [],
])

@php
    $totalReports = $stats['totalReports'] ?? 0;
    $byDamageLevel = $stats['byDamageLevel'] ?? ['minimal' => 0, 'partial' => 0, 'complete' => 0];
    $byCrisisType = $stats['byCrisisType'] ?? ['natural' => 0, 'technological' => 0, 'human-made' => 0];
    $recentReports = $stats['recentReports'] ?? [];
    $syncedCount = $stats['syncedCount'] ?? 0;
    $pendingCount = $stats['pendingCount'] ?? 0;
@endphp

<div
    {{ $attributes->class(['w-full space-y-6']) }}
    role="region"
    aria-label="UNDP Analytics Dashboard"
>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-h2 font-heading font-bold text-slate-900">Analytics Dashboard</h2>
        <x-molecules.notification type="info" message="Data refreshes every 30 seconds." />
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" role="list" aria-label="Key performance indicators">
        {{-- Total Reports --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-2" role="listitem">
            <p class="text-caption font-medium text-slate-500 uppercase tracking-widest">Total Reports</p>
            <p class="text-display font-heading font-bold text-rapida-blue-900">{{ $totalReports }}</p>
            <x-atoms.badge variant="synced">{{ $syncedCount }} synced</x-atoms.badge>
        </div>

        {{-- Minimal --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-2" role="listitem">
            <p class="text-caption font-medium text-slate-500 uppercase tracking-widest">Minimal Damage</p>
            <p class="text-display font-heading font-bold text-ground-green-700">{{ $byDamageLevel['minimal'] ?? 0 }}</p>
            <x-atoms.badge variant="minimal">Minimal</x-atoms.badge>
        </div>

        {{-- Partial --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-2" role="listitem">
            <p class="text-caption font-medium text-slate-500 uppercase tracking-widest">Partial Damage</p>
            <p class="text-display font-heading font-bold text-alert-amber-700">{{ $byDamageLevel['partial'] ?? 0 }}</p>
            <x-atoms.badge variant="partial">Partial</x-atoms.badge>
        </div>

        {{-- Complete --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-2" role="listitem">
            <p class="text-caption font-medium text-slate-500 uppercase tracking-widest">Complete Destruction</p>
            <p class="text-display font-heading font-bold text-crisis-rose-700">{{ $byDamageLevel['complete'] ?? 0 }}</p>
            <x-atoms.badge variant="complete">Complete</x-atoms.badge>
        </div>
    </div>

    {{-- Sync Status --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">Sync Status</h3>
        <div class="flex items-center gap-4">
            <x-atoms.badge variant="synced" size="lg">{{ $syncedCount }} Synced</x-atoms.badge>
            @if($pendingCount > 0)
                <x-atoms.badge variant="pending" size="lg">{{ $pendingCount }} Pending</x-atoms.badge>
            @endif
        </div>
    </div>

    {{-- Crisis Type Breakdown --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">By Crisis Type</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <x-atoms.badge variant="info">Natural</x-atoms.badge>
                <span class="text-h4 font-semibold text-slate-900">{{ $byCrisisType['natural'] ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <x-atoms.badge variant="info">Technological</x-atoms.badge>
                <span class="text-h4 font-semibold text-slate-900">{{ $byCrisisType['technological'] ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                <x-atoms.badge variant="info">Human-made</x-atoms.badge>
                <span class="text-h4 font-semibold text-slate-900">{{ $byCrisisType['human-made'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div>
        <h3 class="text-h4 font-heading font-semibold text-slate-900 mb-3">Recent Reports</h3>
        @if(count($recentReports) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($recentReports as $report)
                    <x-molecules.damage-report-card
                        :photo="$report['photo'] ?? null"
                        :damageLevel="$report['damageLevel'] ?? 'minimal'"
                        :infrastructureType="$report['infrastructureType'] ?? null"
                        :location="$report['location'] ?? 'Unknown'"
                        :description="$report['description'] ?? null"
                        :reporterName="$report['reporterName'] ?? null"
                        :submittedAt="$report['submittedAt'] ?? null"
                        :syncStatus="$report['syncStatus'] ?? 'synced'"
                        variant="compact"
                    />
                @endforeach
            </div>
        @else
            <p class="text-body text-slate-500 py-4">No recent reports to display.</p>
        @endif
    </div>
</div>
