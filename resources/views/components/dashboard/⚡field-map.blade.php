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
        $query = DamageReport::query();

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
        <x-atoms.select name="damageFilter" wire:model.live="damageFilter">
            <option value="">All Damage Levels</option>
            <option value="minimal">Minimal</option>
            <option value="partial">Partial</option>
            <option value="complete">Complete</option>
        </x-atoms.select>

        <x-atoms.select name="infraFilter" wire:model.live="infraFilter">
            <option value="">All Infrastructure</option>
            <option value="commercial">Commercial</option>
            <option value="government">Government</option>
            <option value="utility">Utility</option>
            <option value="transport">Transport</option>
            <option value="community">Community</option>
            <option value="public_recreation">Public Recreation</option>
            <option value="other">Other</option>
        </x-atoms.select>

        <div class="ml-auto flex items-center gap-2 text-body-sm text-slate-600">
            <span class="font-semibold text-slate-900">{{ $this->reportCount }}</span>
            <span>reports</span>
        </div>
    </div>

    {{-- Map area --}}
    <div class="flex-1 relative min-h-[400px]">
        @if($crisisSlug)
            <x-organisms.map-organism
                height="h-[500px]"
                :crisisSlug="$crisisSlug"
                :centerLat="5.56"
                :centerLng="-0.20"
                :zoom="13"
                mode="dashboard"
            />
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-surface-page text-slate-600">
                <div class="text-center">
                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <p class="text-body-sm font-medium">Field Map</p>
                    <p class="text-caption text-slate-400 mt-1">No active crisis</p>
                </div>
            </div>
        @endif
    </div>
</div>
