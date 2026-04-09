@props([
    'crisisSlug',
    'crisisName' => 'RAPIDA',
    'conflictContext' => false,
])

<div
    x-data="{
        shown: !localStorage.getItem('rapida_onboarded_{{ $crisisSlug }}'),
        dismiss() {
            localStorage.setItem('rapida_onboarded_{{ $crisisSlug }}', 'true');
            this.shown = false;
        }
    }"
    x-show="shown"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-surface-page px-padding-page-x-mob"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('rapida.transparency_standard_cta') }}"
>
    <div class="w-full max-w-md mx-auto text-center space-y-element">
        {{-- Logo + crisis name --}}
        <div class="space-y-nano">
            <x-atoms.icon name="check-circle" size="xl" class="text-rapida-blue-700 mx-auto" />
            <h1 class="text-h2 font-heading font-bold text-rapida-blue-900 tracking-tight">{{ $crisisName }}</h1>
        </div>

        {{-- What is this? --}}
        <p class="text-body text-text-secondary">
            {{ __('onboarding.what_is_rapida') }}
        </p>

        {{-- Data use explanation --}}
        <div class="text-left space-y-micro">
            @if($conflictContext)
                {{-- Conflict mode: lead with anonymity, no location mention --}}
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_conflict_1') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_conflict_2') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_conflict_3') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_conflict_4') }}</span>
                </div>
            @else
                {{-- Standard mode: positive framing first --}}
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_standard_1') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_standard_2') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_standard_3') }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-ground-green-700 shrink-0 mt-0.5">&#10003;</span>
                    <span class="text-body-sm text-text-primary">{{ __('rapida.transparency_standard_4') }}</span>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="space-y-micro pt-element">
            <x-atoms.button
                variant="primary"
                class="w-full"
                @click="dismiss()"
            >
                {{ $conflictContext ? __('rapida.transparency_conflict_cta') : __('rapida.transparency_standard_cta') }}
            </x-atoms.button>

            <button
                type="button"
                x-data="{ open: false }"
                @click="open = !open"
                class="text-body-sm font-medium text-rapida-blue-700 hover:text-rapida-blue-900 transition-colors"
            >
                {{ $conflictContext ? __('rapida.transparency_conflict_learn_more') : __('rapida.transparency_standard_learn_more') }}
            </button>
        </div>
    </div>
</div>
