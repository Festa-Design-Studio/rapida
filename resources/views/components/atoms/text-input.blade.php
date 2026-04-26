@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'value' => null,
    'leadingIcon' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $help ? "{$inputId}-help" : null;
    $errorId = $error ? "{$inputId}-error" : null;
    $describedBy = collect([$errorId, $helpId])->filter()->implode(' ') ?: null;

    $inputClasses = 'h-12 w-full px-4 py-3
        text-body text-slate-900 font-sans
        border rounded-lg
        placeholder:text-slate-400
        focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
        disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed
        transition-colors duration-150';

    $borderClass = match(true) {
        (bool) $error   => 'border-crisis-rose-700 bg-crisis-rose-50 focus:ring-crisis-rose-700 focus:border-crisis-rose-700',
        $readonly       => 'border-slate-200 bg-slate-50',
        $disabled       => 'border-slate-200 bg-slate-50',
        default         => 'border-slate-300 bg-white',
    };

    if ($leadingIcon) {
        $inputClasses .= ' ps-10';
    }
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-label font-medium text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-crisis-rose-700 ms-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($leadingIcon)
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                {{ $leadingIcon }}
            </span>
        @endif

        <input
            id="{{ $inputId }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required aria-required="true" @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly aria-readonly="true" @endif
            @if($error) aria-invalid="true" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class([$inputClasses, $borderClass]) }}
        />
    </div>

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-crisis-rose-700 flex items-center gap-1">
            {{ $error }}
        </p>
    @elseif($help)
        <p id="{{ $helpId }}" class="text-body-sm text-slate-500">
            {{ $help }}
        </p>
    @endif
</div>
