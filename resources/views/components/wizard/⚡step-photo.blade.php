<?php

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Crisis;

new class extends Component {
    use WithFileUploads;

    public Crisis $crisis;

    public bool $conflictMode = false;

    public $photo = null;

    public function completeStep(): void
    {
        $this->dispatch('step-completed', data: [
            'photo' => $this->photo,
        ]);
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Photo guidance drawer (pre-screen, once per session) --}}
    <x-molecules.photo-guidance-drawer :crisisType="$crisis->crisis_type_default ?? 'earthquake'" />

    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_1_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_1_desc') }}</p>
    </div>

    <div class="flex flex-col gap-1.5">
        <span class="text-label font-medium text-slate-700">{{ __('wizard.step_1_label') }}</span>

        @if($photo)
            {{-- Preview --}}
            <div class="relative rounded-xl border-2 border-rapida-blue-700 overflow-hidden">
                <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('wizard.step_1_label') }}" class="w-full max-h-64 object-cover" />
                <div class="flex items-center justify-center gap-4 px-4 py-2 bg-slate-50 border-t border-slate-200">
                    <label for="photo-replace" class="text-body-sm font-medium text-rapida-blue-700 hover:text-rapida-blue-900 cursor-pointer">{{ __('wizard.step_1_change') }}</label>
                    <span class="text-slate-300">|</span>
                    <button type="button" wire:click="$set('photo', null)" class="text-body-sm font-medium text-crisis-rose-700 hover:text-crisis-rose-900">{{ __('wizard.step_1_remove') }}</button>
                </div>
                <input id="photo-replace" type="file" accept="image/*" capture="environment" wire:model="photo" class="sr-only" />
            </div>
        @else
            {{-- Empty upload zone --}}
            <label
                for="photo-input"
                class="relative flex flex-col items-center justify-center min-h-[160px] rounded-xl border-2 border-dashed border-slate-300 bg-slate-50
                       hover:border-rapida-blue-500 hover:bg-rapida-blue-50 cursor-pointer transition-colors duration-150"
            >
                <div class="flex flex-col items-center gap-3 p-6 text-center">
                    <x-atoms.icon name="camera" size="xl" class="text-slate-400" />
                    <div>
                        <p class="text-body-sm font-medium text-slate-700">{{ __('wizard.step_1_upload_prompt') }}</p>
                        <p class="text-caption text-slate-400 mt-1">{{ __('wizard.step_1_upload_formats') }}</p>
                    </div>
                </div>
                <input id="photo-input" type="file" accept="image/*" capture="environment" wire:model="photo" class="sr-only" />
            </label>
        @endif

        {{-- Loading state --}}
        <div wire:loading wire:target="photo" class="flex items-center gap-2 text-body-sm text-rapida-blue-700">
            <x-atoms.loader size="sm" />
            <span>{{ __('wizard.step_1_uploading') }}</span>
        </div>

        @error('photo')
            <p class="text-body-sm text-crisis-rose-700" role="alert">{{ $message }}</p>
        @enderror

        <p class="text-body-sm text-slate-500">{{ __('wizard.step_1_help') }}</p>
    </div>
</div>
