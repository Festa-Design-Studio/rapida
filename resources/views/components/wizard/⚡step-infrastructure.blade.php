<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">What type of building?</h1>
        <p class="text-body text-slate-600">
            Tell us more about the damaged structure.
        </p>
    </div>

    {{-- Infrastructure type selection (checkbox group) --}}
    <x-molecules.infrastructure-type
        name="infrastructure_type"
        :values="$this->parent->infrastructureTypes ?? []"
        wire:model.live="$parent.infrastructureTypes"
    />

    {{-- Crisis type selection (radio group) --}}
    <x-molecules.crisis-type
        name="crisis_type"
        :value="$this->parent->crisisType ?? ''"
        wire:model.live="$parent.crisisType"
    />

    {{-- Debris question --}}
    <x-atoms.radio-group
        name="debris_required"
        legend="Is debris blocking access?"
        variant="standard"
        :options="[
            'yes' => 'Yes',
            'no' => 'No',
            'unknown' => 'Not sure',
        ]"
        :value="match($this->parent->debrisRequired ?? null) {
            true => 'yes',
            false => 'no',
            null => 'unknown',
            default => null,
        }"
        wire:model.live="$parent.debrisRequired"
    />

    {{-- Infrastructure name --}}
    <x-atoms.text-input
        name="infrastructure_name"
        label="Building or place name"
        placeholder="e.g. Al-Nour Hospital"
        help="Optional. Helps identify this specific structure."
        wire:model.live.debounce.500ms="$parent.infrastructureName"
    />

    {{-- Description --}}
    <x-atoms.textarea
        name="description"
        label="Additional details"
        placeholder="Describe what you see — anything that might help responders."
        help="Optional. Up to 500 characters."
        :maxlength="500"
        wire:model.live.debounce.500ms="$parent.description"
    />
</div>
