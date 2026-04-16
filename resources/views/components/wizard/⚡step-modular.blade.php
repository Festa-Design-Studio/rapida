<?php

use Livewire\Component;
use App\Models\Crisis;

new class extends Component {
    public Crisis $crisis;

    public bool $conflictMode = false;

    public array $moduleResponses = [
        'electricity_condition' => '',
        'health_functioning' => '',
        'pressing_needs_needs' => [],
    ];

    public function completeStep(): void
    {
        $this->dispatch('step-completed', data: [
            'moduleResponses' => $this->moduleResponses,
        ]);
    }
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_5_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_5_desc') }}</p>
    </div>

    @php
        $activeModules = $crisis->active_modules ?? ['electricity', 'health', 'pressing_needs'];
    @endphp

    @foreach($activeModules as $moduleKey)
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            @if($moduleKey === 'electricity')
                <fieldset class="flex flex-col gap-3">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_electricity') }}</legend>
                    <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_electricity_q') }}</p>
                    @foreach([
                        'no_damage' => 'module_option_no_damage',
                        'minor' => 'module_option_minor',
                        'moderate' => 'module_option_moderate',
                        'severe' => 'module_option_severe',
                        'destroyed' => 'module_option_destroyed',
                        'unknown' => 'module_option_unknown',
                    ] as $optValue => $optKey)
                        <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ ($moduleResponses['electricity_condition'] ?? '') === $optValue ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="radio" name="electricity_condition" value="{{ $optValue }}"
                                   wire:model.live="moduleResponses.electricity_condition"
                                   class="h-5 w-5 accent-rapida-blue-700" />
                            <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                        </label>
                    @endforeach
                </fieldset>

            @elseif($moduleKey === 'health')
                <fieldset class="flex flex-col gap-3">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_health') }}</legend>
                    <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_health_q') }}</p>
                    @foreach([
                        'fully_functional' => 'module_option_fully_functional',
                        'partially_functional' => 'module_option_partially_functional',
                        'largely_disrupted' => 'module_option_largely_disrupted',
                        'not_functioning' => 'module_option_not_functioning',
                        'unknown' => 'module_option_unknown',
                    ] as $optValue => $optKey)
                        <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ ($moduleResponses['health_functioning'] ?? '') === $optValue ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="radio" name="health_functioning" value="{{ $optValue }}"
                                   wire:model.live="moduleResponses.health_functioning"
                                   class="h-5 w-5 accent-rapida-blue-700" />
                            <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                        </label>
                    @endforeach
                </fieldset>

            @elseif($moduleKey === 'pressing_needs')
                <fieldset class="flex flex-col gap-3">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_pressing_needs') }}</legend>
                    <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_pressing_needs_q') }}</p>
                    @foreach([
                        'food_water' => 'needs_food_water',
                        'cash' => 'needs_cash',
                        'healthcare' => 'needs_healthcare',
                        'shelter' => 'needs_shelter',
                        'livelihoods' => 'needs_livelihoods',
                        'wash' => 'needs_wash',
                        'infrastructure' => 'needs_infrastructure',
                        'protection' => 'needs_protection',
                        'authority' => 'needs_authority',
                        'other' => 'needs_other',
                    ] as $optValue => $optKey)
                        <label class="flex items-start gap-3 px-4 py-3 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ in_array($optValue, $moduleResponses['pressing_needs_needs'] ?? []) ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="checkbox" value="{{ $optValue }}"
                                   wire:model.live="moduleResponses.pressing_needs_needs"
                                   class="mt-0.5 h-5 w-5 rounded accent-rapida-blue-700" />
                            <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                        </label>
                    @endforeach
                </fieldset>
            @endif
        </div>
    @endforeach
</div>
