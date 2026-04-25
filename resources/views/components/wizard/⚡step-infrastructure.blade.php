<?php

use Livewire\Component;
use App\Models\Crisis;

new class extends Component {
    public Crisis $crisis;

    public bool $conflictMode = false;

    public array $infrastructureTypes = [];

    public string $crisisType = '';

    public ?string $infrastructureName = null;

    public ?string $debrisRequired = null;

    public ?string $description = null;

    public function completeStep(): void
    {
        $this->dispatch('step-completed', data: [
            'infrastructureTypes' => $this->infrastructureTypes,
            'crisisType' => $this->crisisType,
            'infrastructureName' => $this->infrastructureName,
            'debrisRequired' => $this->debrisRequired,
            'description' => $this->description,
        ]);
    }
};
?>

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_4_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_4_desc') }}</p>
    </div>

    {{-- Infrastructure type --}}
    <fieldset class="flex flex-col gap-2">
        <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.infra_type_label') }} <span class="text-crisis-rose-700">*</span></legend>
        <p class="text-body-sm text-slate-500 mb-2">{{ __('wizard.infra_select_all') }}</p>
        @foreach([
            'commercial' => ['infra_commercial', 'infra_commercial_desc'],
            'government' => ['infra_government', 'infra_government_desc'],
            'utility' => ['infra_utility', 'infra_utility_desc'],
            'transport' => ['infra_transport', 'infra_transport_desc'],
            'community' => ['infra_community', 'infra_community_desc'],
            'public_recreation' => ['infra_public_recreation', 'infra_public_recreation_desc'],
            'other' => ['infra_other', 'infra_other_desc'],
        ] as $value => [$labelKey, $descKey])
            <label class="flex items-start gap-3 px-4 py-3 rounded-lg border cursor-pointer transition-colors duration-150
                        {{ in_array($value, $infrastructureTypes) ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                <input type="checkbox" value="{{ $value }}" wire:model.live="infrastructureTypes"
                       class="mt-0.5 h-5 w-5 rounded accent-rapida-blue-700" />
                <div>
                    <p class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</p>
                    <p class="text-body-sm text-slate-500">{{ __("wizard.{$descKey}") }}</p>
                </div>
            </label>
        @endforeach
    </fieldset>

    {{-- Crisis type --}}
    <fieldset class="flex flex-col gap-2">
        <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.crisis_type_label') }} <span class="text-crisis-rose-700">*</span></legend>
        @foreach([
            'flood' => 'crisis_flood',
            'earthquake' => 'crisis_earthquake',
            'hurricane' => 'crisis_hurricane',
            'wildfire' => 'crisis_wildfire',
            'explosion' => 'crisis_explosion',
            'conflict' => 'crisis_conflict',
        ] as $value => $labelKey)
            <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                        {{ $crisisType === $value ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                <input type="radio" name="crisis_type" value="{{ $value }}" wire:model.live="crisisType"
                       class="h-5 w-5 accent-rapida-blue-700" />
                <span class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</span>
            </label>
        @endforeach
    </fieldset>

    {{-- Debris --}}
    <fieldset class="flex flex-col gap-2">
        <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.debris_label') }}</legend>
        @foreach(['yes' => 'debris_yes', 'no' => 'debris_no', 'unknown' => 'debris_unknown'] as $value => $labelKey)
            <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                        {{ $debrisRequired === $value ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                <input type="radio" name="debris_required" value="{{ $value }}" wire:model.live="debrisRequired"
                       class="h-5 w-5 accent-rapida-blue-700" />
                <span class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</span>
            </label>
        @endforeach
    </fieldset>

    {{-- Name + description --}}
    <x-atoms.text-input
        name="infrastructure_name"
        :label="__('wizard.infra_name_label')"
        :placeholder="__('wizard.infra_name_placeholder')"
        :help="__('wizard.infra_name_help')"
        wire:model.live.debounce.500ms="infrastructureName"
    />

    <x-atoms.textarea
        name="description"
        :label="__('wizard.description_label')"
        :placeholder="__('wizard.description_placeholder')"
        :help="__('wizard.description_help')"
        :maxlength="500"
        wire:model.live.debounce.500ms="description"
    />
</div>
