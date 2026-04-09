@props([
    'reports' => [],
    'loading' => false,
    'emptyMessage' => 'No damage reports yet. Be the first to submit one.',
])

<div
    {{ $attributes->class(['w-full']) }}
    role="feed"
    aria-label="Community damage reports"
    aria-busy="{{ $loading ? 'true' : 'false' }}"
>
    {{-- Offline queue status --}}
    <div class="mb-4">
        <x-molecules.offline-queue status="online" :pendingCount="0" />
    </div>

    {{-- Loading state --}}
    @if($loading)
        <div class="space-y-4" aria-label="Loading reports">
            @for($i = 0; $i < 3; $i++)
                <x-atoms.loader variant="skeleton-card" />
            @endfor
        </div>
    @elseif(count($reports) === 0)
        {{-- Empty state --}}
        <div class="rounded-xl border border-slate-200 bg-white p-8 text-center" role="status">
            <x-atoms.icon name="inbox" size="xl" class="text-slate-300 mx-auto mb-3" />
            <p class="text-body text-slate-600">{{ $emptyMessage }}</p>
            <div class="mt-4">
                <x-atoms.button variant="primary" size="md">
                    Submit a Report
                </x-atoms.button>
            </div>
        </div>
    @else
        {{-- Report list --}}
        <div class="space-y-4">
            @foreach($reports as $index => $report)
                <x-molecules.damage-report-card
                    :photo="$report['photo'] ?? null"
                    :damageLevel="$report['damageLevel'] ?? 'minimal'"
                    :infrastructureType="$report['infrastructureType'] ?? null"
                    :location="$report['location'] ?? 'Unknown location'"
                    :description="$report['description'] ?? null"
                    :reporterName="$report['reporterName'] ?? null"
                    :submittedAt="$report['submittedAt'] ?? null"
                    :syncStatus="$report['syncStatus'] ?? 'pending'"
                    :href="isset($report['id']) ? route('report-detail', $report['id']) : null"
                    aria-setsize="{{ count($reports) }}"
                    aria-posinset="{{ $index + 1 }}"
                />
            @endforeach
        </div>
    @endif
</div>
