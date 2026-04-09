<?php

use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    #[Modelable]
    public string $value = '';
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">How bad is the damage?</h1>
        <p class="text-body text-slate-600">
            Select the level that best describes what you see.
        </p>
    </div>

    <x-molecules.damage-classification
        name="damage_level"
        :value="$value"
        required
        wire:model.live="value"
    />
</div>
