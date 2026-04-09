@props([
    'type' => 'info',
    'message',
    'dismissible' => false,
    'action' => null,
])

@php
    $colors = match($type) {
        'success' => 'bg-ground-green-50 border-ground-green-200 text-ground-green-900',
        'warning' => 'bg-alert-amber-50 border-alert-amber-100 text-alert-amber-900',
        'error'   => 'bg-crisis-rose-50 border-crisis-rose-100 text-crisis-rose-900',
        default   => 'bg-rapida-blue-50 border-rapida-blue-100 text-rapida-blue-900',
    };

    $iconName = match($type) {
        'success' => 'check-circle',
        'warning' => 'alert-triangle',
        'error'   => 'alert-circle',
        default   => 'info',
    };

    $roleAttr = match($type) {
        'error'   => 'alert',
        'warning' => 'alert',
        default   => 'status',
    };
@endphp

<div
    {{ $attributes->class([
        'rounded-lg p-4 border flex items-start gap-3',
        $colors,
    ]) }}
    role="{{ $roleAttr }}"
    @if($type === 'info' || $type === 'success') aria-live="polite" @endif
    @if($dismissible) x-data="{ visible: true }" x-show="visible" x-transition @endif
>
    <x-atoms.icon :name="$iconName" size="sm" class="shrink-0 mt-0.5" />

    <div class="flex-1 min-w-0">
        <p class="text-body-sm font-medium">{{ $message }}</p>

        @if($action)
            <div class="mt-2">
                <x-atoms.button
                    variant="ghost"
                    size="sm"
                    :href="$action['url'] ?? null"
                >
                    {{ $action['label'] ?? 'Action' }}
                </x-atoms.button>
            </div>
        @endif
    </div>

    @if($dismissible)
        <x-atoms.button
            variant="ghost"
            size="sm"
            class="shrink-0 -mt-1 -mr-1"
            @click="visible = false"
            aria-label="Dismiss notification"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </x-atoms.button>
    @endif
</div>
