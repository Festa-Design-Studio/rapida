<?php

use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    #[Modelable]
    public ?string $value = null;

    #[Reactive]
    public string $crisisSlug = 'accra-flood-2026';
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">Locate the damage</h1>
        <p class="text-body text-slate-600">
            Tap the building on the map, or describe the location below.
        </p>
    </div>

    {{-- MapLibre GL map --}}
    <div
        x-data="typeof rapidaMap !== 'undefined' ? rapidaMap({
            crisisSlug: '{{ $crisisSlug }}',
            mode: 'reporter',
            center: [-0.20, 5.56],
            zoom: 14,
            tokens: {
                damage_minimal: '#22c55e',   // damage-minimal-map
                damage_partial: '#f59e0b',   // damage-partial-map
                damage_complete: '#c46b5a',  // crisis-rose-400
                footprint_fill: '#2e6689',   // rapida-blue-700
                footprint_stroke: '#1a3a4a', // rapida-blue-900
                user_dot: '#2e6689',         // rapida-blue-700
            },
            buildingsUrl: '/api/v1/crises/{{ $crisisSlug }}/buildings',
            pinsUrl: '/api/v1/crises/{{ $crisisSlug }}/pins',
        }) : {}"
        x-init="if (typeof rapidaMap !== 'undefined' && init) init()"
        wire:ignore
        id="rapida-map-wizard"
        class="w-full h-[300px] rounded-lg overflow-hidden bg-slate-100"
        role="application"
        aria-label="Map — tap a building to select its location"
    ></div>

    <p class="text-body-sm text-slate-500 -mt-4">
        Or describe the location using a landmark:
    </p>

    <x-atoms.text-input
        name="landmark_text"
        label="Location description"
        placeholder="e.g. Near the central market, second building on the left"
        help="A landmark or street name helps responders find this location."
        wire:model.live.debounce.500ms="value"
    />
</div>
