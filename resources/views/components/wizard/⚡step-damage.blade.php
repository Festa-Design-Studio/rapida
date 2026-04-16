<?php

use Livewire\Component;
use App\Models\Crisis;

new class extends Component {
    public Crisis $crisis;

    public bool $conflictMode = false;

    public string $damageLevel = '';

    public ?string $aiSuggestedLevel = null;

    public ?float $aiConfidence = null;

    public bool $hasPhoto = false;

    public function completeStep(): void
    {
        $this->dispatch('step-completed', data: [
            'damageLevel' => $this->damageLevel,
        ]);
    }
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_3_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_3_desc') }}</p>
    </div>

    {{-- AI turn-taking (Gap V6b) --}}
    @if($aiSuggestedLevel)
        <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 p-4 flex items-start gap-3">
            <x-atoms.icon name="info" size="md" class="text-rapida-blue-700 shrink-0 mt-0.5" />
            <div>
                @if($aiConfidence !== null)
                    @php
                        $wizardConfidencePercent = round($aiConfidence * 100);
                        $wizardConfidenceTier = $aiConfidence > 0.85 ? 'high' : ($aiConfidence >= 0.60 ? 'medium' : 'low');
                    @endphp
                    <p class="text-body-sm font-medium text-rapida-blue-900">
                        {{ __('rapida.ai_suggestion_with_confidence', ['level' => ucfirst($aiSuggestedLevel), 'percent' => $wizardConfidencePercent . '%']) }}
                    </p>
                    <div class="mt-1.5">
                        <x-atoms.badge :variant="'confidence-' . $wizardConfidenceTier">{{ __('rapida.ai_confidence_' . $wizardConfidenceTier) }}</x-atoms.badge>
                    </div>
                @else
                    <p class="text-body-sm font-medium text-rapida-blue-900">
                        {{ __('rapida.ai_suggestion_prompt', ['level' => ucfirst($aiSuggestedLevel)]) }}
                    </p>
                @endif
            </div>
        </div>
    @elseif(! $damageLevel && $hasPhoto)
        {{-- AI is still processing — show analysing state --}}
        <div class="flex items-center gap-2 text-body-sm text-rapida-blue-700">
            <x-atoms.loader size="sm" />
            <span>{{ __('rapida.ai_analyzing') }}</span>
        </div>
    @endif

    <fieldset class="flex flex-col gap-3" wire:model.live="damageLevel">
        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                    {{ $damageLevel === 'minimal' ? 'border-damage-minimal-ui bg-damage-minimal-ui-surface' : 'border-slate-200 hover:border-ground-green-500' }}">
            <input type="radio" name="damage_level" value="minimal" wire:model.live="damageLevel" class="sr-only" />
            <div class="w-4 h-4 rounded-full bg-damage-minimal-map shrink-0"></div>
            <div class="flex-1">
                <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_minimal') }}</p>
                <p class="text-body-sm text-slate-500">{{ __('wizard.damage_minimal_desc') }}</p>
            </div>
        </label>

        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                    {{ $damageLevel === 'partial' ? 'border-damage-partial-ui bg-damage-partial-ui-surface' : 'border-slate-200 hover:border-alert-amber-500' }}">
            <input type="radio" name="damage_level" value="partial" wire:model.live="damageLevel" class="sr-only" />
            <div class="w-4 h-4 rounded-full bg-damage-partial-map shrink-0"></div>
            <div class="flex-1">
                <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_partial') }}</p>
                <p class="text-body-sm text-slate-500">{{ __('wizard.damage_partial_desc') }}</p>
            </div>
        </label>

        <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                    {{ $damageLevel === 'complete' ? 'border-damage-complete-ui bg-damage-complete-ui-surface' : 'border-slate-200 hover:border-crisis-rose-400' }}">
            <input type="radio" name="damage_level" value="complete" wire:model.live="damageLevel" class="sr-only" />
            <div class="w-4 h-4 rounded-full bg-damage-complete-map shrink-0"></div>
            <div class="flex-1">
                <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_complete') }}</p>
                <p class="text-body-sm text-slate-500">{{ __('wizard.damage_complete_desc') }}</p>
            </div>
        </label>
    </fieldset>
</div>
