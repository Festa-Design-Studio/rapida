<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    public string $crisisSlug = '';

    public int $totalReports = 0;

    public function mount(): void
    {
        $crisis = Crisis::where('status', 'active')->first();
        $this->crisisSlug = $crisis?->slug ?? '';
        $this->totalReports = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)->where('is_flagged', false)->count()
            : 0;
    }
};
?>

<div
    x-data="{
        damageFilter: '',
        infraFilter: '',
        baseUrl: '/api/v1/crises/{{ $crisisSlug }}/pins',

        applyFilter() {
            const mapEl = document.querySelector('[x-ref=mapEl]');
            const api = mapEl?.__rapidaMapApi;
            if (!api) return;
            let url = this.baseUrl;
            const params = [];
            if (this.damageFilter) params.push('damage_level=' + this.damageFilter);
            if (this.infraFilter) params.push('infrastructure_type=' + this.infraFilter);
            if (params.length) url += '?' + params.join('&');
            api.refetchPins(url);
        }
    }"
    class="flex flex-col h-full"
>
    {{-- Filter bar --}}
    <div class="flex flex-wrap items-center gap-3 bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex flex-col gap-1.5">
            <select
                x-model="damageFilter"
                @change="applyFilter()"
                aria-label="{{ __('rapida.damage_level_label') }}"
                class="h-10 px-3 pr-8 text-body-sm font-sans border border-slate-300 rounded-lg bg-white
                       focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
                       appearance-none cursor-pointer transition-colors duration-fast"
            >
                <option value="">{{ __('rapida.damage_level_label') }}</option>
                <option value="minimal">{{ __('rapida.damage_minimal') }}</option>
                <option value="partial">{{ __('rapida.damage_partial') }}</option>
                <option value="complete">{{ __('rapida.damage_complete') }}</option>
            </select>
        </div>

        <div class="flex flex-col gap-1.5">
            <select
                x-model="infraFilter"
                @change="applyFilter()"
                aria-label="{{ __('rapida.infrastructure') }}"
                class="h-10 px-3 pr-8 text-body-sm font-sans border border-slate-300 rounded-lg bg-white
                       focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
                       appearance-none cursor-pointer transition-colors duration-fast"
            >
                <option value="">{{ __('rapida.infrastructure') }}</option>
                <option value="commercial">{{ __('wizard.infra_commercial') }}</option>
                <option value="government">{{ __('wizard.infra_government') }}</option>
                <option value="utility">{{ __('wizard.infra_utility') }}</option>
                <option value="transport">{{ __('wizard.infra_transport') }}</option>
                <option value="community">{{ __('wizard.infra_community') }}</option>
                <option value="public_recreation">{{ __('wizard.infra_public_recreation') }}</option>
                <option value="other">{{ __('wizard.infra_other') }}</option>
            </select>
        </div>

        <div class="ml-auto flex items-center gap-2 text-body-sm text-text-secondary">
            <span class="font-semibold text-text-primary">{{ $totalReports }}</span>
            <span>{{ __('rapida.recent_reports') }}</span>
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
                    tokens: @json(config('rapida-tokens.map')),
                    buildingsUrl: '/api/v1/crises/{{ $crisisSlug }}/buildings',
                    pinsUrl: '/api/v1/crises/{{ $crisisSlug }}/pins',
                    heatmapUrl: '',
                })"
                x-ref="mapEl"
                wire:ignore
                x-init="init()"
                class="absolute inset-0"
                aria-label="{{ __('rapida.location') }}"
            ></div>

            <x-molecules.map-legend class="absolute bottom-4 left-3 z-10" />
        @else
            <div class="absolute inset-0 flex items-center justify-center bg-surface-page">
                <div class="text-center">
                    <x-atoms.icon name="compass" size="xl" class="text-text-placeholder mx-auto mb-3" />
                    <p class="text-body-sm font-medium text-text-primary">{{ __('rapida.location') }}</p>
                    <p class="text-caption text-text-placeholder mt-1">{{ __('rapida.empty_map') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
