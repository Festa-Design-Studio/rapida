@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 4,
    'maxlength' => null,
    'value' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $help ? "{$inputId}-help" : null;
    $errorId = $error ? "{$inputId}-error" : null;
    $countId = $maxlength ? "{$inputId}-count" : null;
    $describedBy = collect([$errorId, $helpId, $countId])->filter()->implode(' ') ?: null;

    $borderClass = match(true) {
        (bool) $error => 'border-crisis-rose-700 bg-crisis-rose-50 focus:ring-crisis-rose-700 focus:border-crisis-rose-700',
        $disabled     => 'border-slate-200 bg-slate-50',
        default       => 'border-slate-300 bg-white',
    };
@endphp

<div
    class="flex flex-col gap-1.5"
    @if($maxlength) x-data="{ text: '{{ addslashes($value ?? '') }}', maxLength: {{ $maxlength }} }" @endif
>
    @if($label)
        <label for="{{ $inputId }}" class="text-label font-medium text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-crisis-rose-700 ms-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required aria-required="true" @endif
        @if($disabled) disabled @endif
        @if($maxlength) maxlength="{{ $maxlength }}" x-model="text" @endif
        @if($error) aria-invalid="true" @endif
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->class([
            'w-full px-4 py-3 text-body text-slate-900 font-sans leading-relaxed border rounded-lg
            placeholder:text-slate-400 resize-none
            focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
            disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed
            transition-colors duration-150',
            $borderClass,
        ]) }}
    >{{ $value }}</textarea>

    <div class="flex items-start justify-between gap-4">
        @if($error)
            <p id="{{ $errorId }}" role="alert" class="text-body-sm text-crisis-rose-700 flex items-center gap-1 flex-1">
                {{ $error }}
            </p>
        @elseif($help)
            <p id="{{ $helpId }}" class="text-body-sm text-slate-500 flex-1">
                {{ $help }}
            </p>
        @endif

        @if($maxlength)
            <p id="{{ $countId }}"
               class="text-caption shrink-0 tabular-nums"
               x-bind:class="{
                   'text-slate-400': text.length < maxLength * 0.8,
                   'text-amber-600': text.length >= maxLength * 0.8 && text.length < maxLength,
                   'text-crisis-rose-700 font-medium': text.length >= maxLength
               }"
               aria-live="polite"
               x-text="text.length + ' / ' + maxLength">
                0 / {{ $maxlength }}
            </p>
        @endif
    </div>
</div>
