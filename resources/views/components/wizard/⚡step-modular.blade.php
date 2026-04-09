<?php

use Livewire\Attributes\Reactive;
use Livewire\Component;

new class extends Component {
    #[Reactive]
    public string $damageLevel = '';

    #[Reactive]
    public array $infrastructureTypes = [];

    #[Reactive]
    public ?string $landmarkText = null;

    #[Reactive]
    public ?string $photoPath = null;
};
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">Review and send</h1>
        <p class="text-body text-slate-600">
            Check your report before submitting. Every detail helps.
        </p>
    </div>

    {{-- Summary card --}}
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        {{-- Photo thumbnail --}}
        <div class="h-32 bg-slate-100 flex items-center justify-center border-b border-slate-200">
            @if($photoPath)
                <img src="{{ $photoPath }}" alt="Damage photo" class="w-full h-full object-cover" />
            @else
                <div class="flex flex-col items-center gap-1 text-slate-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                    </svg>
                    <p class="text-caption">No photo added</p>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="p-4 flex flex-col gap-3">
            {{-- Damage level --}}
            <div class="flex items-center justify-between">
                <span class="text-body-sm text-slate-500">Damage level</span>
                @if($damageLevel)
                    <x-atoms.badge :variant="$damageLevel" size="lg">
                        {{ ucfirst($damageLevel) }}
                    </x-atoms.badge>
                @else
                    <span class="text-body-sm text-slate-400">Not selected</span>
                @endif
            </div>

            {{-- Infrastructure types --}}
            <div class="flex items-start justify-between gap-4">
                <span class="text-body-sm text-slate-500 shrink-0">Infrastructure</span>
                @if(count($infrastructureTypes) > 0)
                    <div class="flex flex-wrap gap-1.5 justify-end">
                        @foreach($infrastructureTypes as $type)
                            <x-atoms.badge variant="info">
                                {{ ucfirst($type) }}
                            </x-atoms.badge>
                        @endforeach
                    </div>
                @else
                    <span class="text-body-sm text-slate-400">Not selected</span>
                @endif
            </div>

            {{-- Location --}}
            <div class="flex items-center justify-between">
                <span class="text-body-sm text-slate-500">Location</span>
                <span class="text-body-sm text-slate-900 text-right max-w-[200px] truncate">
                    {{ $landmarkText ?: 'Not provided' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Empowerment note --}}
    <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 px-4 py-3">
        <p class="text-body-sm text-rapida-blue-900 text-center">
            You can submit what you have at any time. Partial reports still help.
        </p>
    </div>
</div>
