@props([
    'outcomeId',
    'areaName',
    'message',
    'conflictContext' => false,
])

@if(!$conflictContext)
<div
    x-data="{
        visible: !localStorage.getItem('rapida_outcome_dismissed_{{ $outcomeId }}'),
        dismiss() {
            localStorage.setItem('rapida_outcome_dismissed_{{ $outcomeId }}', 'true');
            this.visible = false;
        }
    }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-calm"
    x-transition:enter-start="translate-y-4 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-fast"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->class([
        'w-full rounded-xl p-padding-card border border-ground-green-200',
        'bg-gradient-to-br from-crisis-rose-50 to-ground-green-50',
        'motion-reduce:transition-none',
    ]) }}
    role="status"
    aria-live="polite"
>
    <div class="flex items-start gap-3">
        <div class="flex-1 min-w-0 space-y-nano">
            <p class="text-label font-medium text-ground-green-900">
                {{ __('rapida.recovery_update_from', ['area' => $areaName]) }}
            </p>
            <p class="text-body-sm text-text-primary">{{ $message }}</p>
            <p class="text-caption text-ground-green-700">{{ __('rapida.recovery_contributed') }}</p>
        </div>

        <button
            type="button"
            @click="dismiss()"
            class="shrink-0 p-2 -mt-1 -mr-1 rounded-lg text-text-secondary hover:text-text-primary hover:bg-white/50 transition-colors"
            aria-label="Dismiss"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
@endif
