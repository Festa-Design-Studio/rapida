<?php

use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component {
    #[Modelable]
    public ?string $value = null;
};
?>

<div
    class="flex flex-col gap-6"
    x-data="{ hasPhoto: false }"
>
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">Take a photo</h1>
        <p class="text-body text-slate-600">
            Point your camera at the damaged building or infrastructure.
        </p>
    </div>

    <x-atoms.photo-upload
        name="damage_photo"
        label="Damage photo"
        help="Your photo helps responders understand the situation on the ground."
        x-on:change="hasPhoto = $event.target.files.length > 0"
    />
</div>
