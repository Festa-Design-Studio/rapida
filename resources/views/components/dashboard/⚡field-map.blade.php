<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    public string $crisisSlug = '';

    public string $damageFilter = '';

    public string $infraFilter = '';

    public function mount(): void
    {
        $crisis = Crisis::where('status', 'active')->first();
        $this->crisisSlug = $crisis?->slug ?? '';
    }

    public function getReportCountProperty(): int
    {
        $crisis = Crisis::where('slug', $this->crisisSlug)->first();

        if (! $crisis) {
            return 0;
        }

        $query = DamageReport::where('crisis_id', $crisis->id)->where('is_flagged', false);

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        if ($this->infraFilter) {
            $query->where('infrastructure_type', $this->infraFilter);
        }

        return $query->count();
    }
};
?>

<div class="flex flex-col h-full">
    {{-- Filter bar --}}
    <div class="flex flex-wrap items-center gap-3 bg-white border-b border-slate-200 px-4 py-3">
        <select
            wire:model.live="damageFilter"
            class="h-10 px-3 pr-8 text-body-sm border border-slate-300 rounded-lg bg-white
                   focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
                   appearance-none cursor-pointer"
        >
            <option value="">All Damage Levels</option>
            <option value="minimal">Minimal</option>
            <option value="partial">Partial</option>
            <option value="complete">Complete</option>
        </select>

        <select
            wire:model.live="infraFilter"
            class="h-10 px-3 pr-8 text-body-sm border border-slate-300 rounded-lg bg-white
                   focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
                   appearance-none cursor-pointer"
        >
            <option value="">All Infrastructure</option>
            <option value="commercial">Commercial</option>
            <option value="government">Government</option>
            <option value="utility">Utility</option>
            <option value="transport">Transport</option>
            <option value="community">Community</option>
            <option value="public_recreation">Public Recreation</option>
            <option value="other">Other</option>
        </select>

        <div class="ml-auto flex items-center gap-2 text-body-sm text-slate-600">
            <span class="font-semibold text-slate-900">{{ $this->reportCount }}</span>
            <span>reports</span>
        </div>
    </div>

    {{-- Map area (fills remaining height) --}}
    <div class="flex-1 relative min-h-[500px]">
        @if($crisisSlug)
            <div
                x-data="rapidaMap({
                    crisisSlug: '{{ $crisisSlug }}',
                    mode: 'dashboard',
                    center: [-0.20, 5.56],
                    zoom: 13,
                    tokens: {
                        damage_minimal: '#22c55e',
                        damage_partial: '#f59e0b',
                        damage_complete: '#c46b5a',
                        footprint_fill: '#2e6689',
                        footprint_stroke: '#1a3a4a',
                        user_dot: '#2e6689',
                    },
                    buildingsUrl: '/api/v1/crises/{{ $crisisSlug }}/buildings',
                    pinsUrl: '/api/v1/crises/{{ $crisisSlug }}/pins{{ $damageFilter ? "?damage_level={$damageFilter}" : "" }}',
                    heatmapUrl: '',
                })"
                x-init="init()"
                wire:ignore
                class="absolute inset-0"
                aria-label="Field damage map"
            ></div>

            <x-molecules.map-legend class="absolute bottom-4 left-3 z-10" />
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-surface-page text-slate-600">
                <div class="text-center">
                    <x-atoms.icon name="compass" size="xl" class="text-slate-400 mx-auto mb-3" />
                    <p class="text-body-sm font-medium">Field Map</p>
                    <p class="text-caption text-slate-400 mt-1">No active crisis</p>
                </div>
            </div>
        @endif
    </div>
</div>
