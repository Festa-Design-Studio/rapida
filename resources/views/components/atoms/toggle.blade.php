@props([
    'name',
    'label',
    'description' => null,
    'enabled' => false,
    'disabled' => false,
])

@php
    $inputId = $attributes->get('id', $name);
    $labelId = "{$inputId}-label";
@endphp

<div
    x-data="{ enabled: @js($enabled) }"
    class="flex items-center justify-between gap-4 py-3"
>
    <div class="flex flex-col gap-0.5">
        <span id="{{ $labelId }}" class="text-body font-medium text-slate-900">
            {{ $label }}
        </span>
        @if($description)
            <span class="text-body-sm text-slate-500">{{ $description }}</span>
        @endif
    </div>

    <button
        type="button"
        role="switch"
        x-bind:aria-checked="enabled.toString()"
        aria-labelledby="{{ $labelId }}"
        @click="enabled = !enabled"
        @keydown.space.prevent="enabled = !enabled"
        x-bind:class="enabled ? 'bg-rapida-blue-700' : 'bg-slate-300'"
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full
               border-2 border-transparent
               focus:outline-none focus:ring-2 focus:ring-rapida-blue-700 focus:ring-offset-2
               transition-colors duration-200 ease-in-out
               {{ $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
        @if($disabled) disabled aria-disabled="true" @endif
    >
        <span class="sr-only" x-text="enabled ? 'On' : 'Off'"></span>
        <span
            x-bind:class="enabled ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow
                   transition-transform duration-200 ease-in-out"
        ></span>
    </button>

    <input type="hidden" name="{{ $name }}" x-bind:value="enabled ? '1' : '0'" />
</div>
