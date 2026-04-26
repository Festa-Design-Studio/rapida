<?php

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Crisis;

new class extends Component {
    public Crisis $crisis;

    public bool $conflictMode = false;

    public ?string $buildingFootprintId = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $locationMethod = 'coordinate_only';

    public ?string $landmarkText = null;

    public function selectLandmark(string $id, float $lat, float $lng, string $name): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->landmarkText = $name;
        $this->locationMethod = 'landmark_picker';
    }

    #[On('building-selected')]
    public function onBuildingSelected(
        string $id,
        float $latitude,
        float $longitude,
        ?string $damage_level = null
    ): void {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        if (str_starts_with($id, 'point-')) {
            $this->buildingFootprintId = null;
            $this->locationMethod = 'coordinate_only';
        } else {
            $this->buildingFootprintId = $id;
            $this->locationMethod = 'footprint_tap';
        }

        $this->dispatch('step-completed', data: [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'buildingFootprintId' => $this->buildingFootprintId,
            'locationMethod' => $this->locationMethod,
            'landmarkText' => $this->landmarkText,
            'damageLevel' => $damage_level,
        ]);
    }

    public function completeStep(): void
    {
        $this->dispatch('step-completed', data: [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'buildingFootprintId' => $this->buildingFootprintId,
            'locationMethod' => $this->locationMethod,
            'landmarkText' => $this->landmarkText,
        ]);
    }
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_2_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_2_desc') }}</p>
    </div>

    @if($latitude && $longitude)
        <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-4 flex items-center gap-3">
            <x-atoms.icon name="check-circle" size="md" class="text-ground-green-700" />
            <div>
                <p class="text-body-sm font-medium text-ground-green-900">{{ __('wizard.step_2_location_selected') }}</p>
                <p class="text-caption text-ground-green-700">{{ number_format($latitude, 5) }}, {{ number_format($longitude, 5) }}</p>
            </div>
        </div>
    @endif

    <div
        x-data="typeof rapidaMap !== 'undefined' ? rapidaMap({
            crisisSlug: '{{ $crisis->slug }}',
            mode: 'reporter',
            center: [-0.20, 5.56],
            zoom: 14,
            tokens: {{ Js::from(config('rapida-tokens.map')) }},
            buildingsUrl: '/api/v1/crises/{{ $crisis->slug }}/buildings',
            pinsUrl: '/api/v1/crises/{{ $crisis->slug }}/pins',
        }) : {}"
        x-init="if (typeof rapidaMap !== 'undefined' && init) init()"
        wire:ignore
        id="rapida-map-wizard"
        class="w-full h-[300px] rounded-lg overflow-hidden bg-slate-100"
        role="application"
        aria-label="{{ __('wizard.map_aria_label') }}"
    ></div>

    <p class="text-body-sm text-slate-500">{{ __('wizard.step_2_or_describe') }}</p>

    <x-atoms.text-input
        name="landmark_text"
        :label="__('wizard.step_2_landmark_label')"
        :placeholder="__('wizard.step_2_landmark_placeholder')"
        :help="__('wizard.step_2_landmark_help')"
        wire:model.live.debounce.500ms="landmarkText"
    />

    {{-- Landmark picker --}}
    @php
        $landmarks = \App\Models\Landmark::where('crisis_id', $crisis->id)->get();
    @endphp
    @if($landmarks->isNotEmpty())
        <div class="mt-4">
            <p class="text-label font-medium text-slate-700 mb-2">{{ __('wizard.landmark_picker_label', [], app()->getLocale()) }}</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach($landmarks as $lm)
                    <button
                        type="button"
                        wire:click="selectLandmark('{{ $lm->id }}', {{ $lm->latitude }}, {{ $lm->longitude }}, '{{ addslashes($lm->name) }}')"
                        class="text-start p-3 rounded-lg border transition-colors duration-150
                               {{ $landmarkText === $lm->name ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500 hover:bg-rapida-blue-50/50' }}"
                    >
                        <p class="text-body-sm font-medium text-slate-900 truncate">{{ $lm->name }}</p>
                        <p class="text-caption text-slate-500">{{ ucfirst($lm->type ?? 'Landmark') }}</p>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
