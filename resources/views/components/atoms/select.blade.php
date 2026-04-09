@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'help' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'options' => [],
    'value' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $helpId = $help ? "{$inputId}-help" : null;
    $errorId = $error ? "{$inputId}-error" : null;
    $describedBy = collect([$errorId, $helpId])->filter()->implode(' ') ?: null;

    $borderClass = match(true) {
        (bool) $error => 'border-red-600 bg-red-50 focus:ring-red-600 focus:border-red-600',
        $disabled     => 'border-slate-200 bg-slate-50',
        default       => 'border-slate-300 bg-white',
    };
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-label font-medium text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-red-600 ml-0.5" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            @if($required) required aria-required="true" @endif
            @if($disabled) disabled @endif
            @if($error) aria-invalid="true" @endif
            @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class([
                'h-12 w-full px-4 py-3 pr-10
                text-body text-slate-900 font-sans
                border rounded-lg appearance-none cursor-pointer
                focus:outline-none focus:border-rapida-blue-700 focus:ring-1 focus:ring-rapida-blue-700
                disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed
                transition-colors duration-150',
                $borderClass,
            ]) }}
        >
            @if($placeholder)
                <option value="" disabled {{ $value === null ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
            {{ $slot }}
        </select>

        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/>
            </svg>
        </div>
    </div>

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-red-700 flex items-center gap-1">
            {{ $error }}
        </p>
    @elseif($help)
        <p id="{{ $helpId }}" class="text-body-sm text-slate-500">
            {{ $help }}
        </p>
    @endif
</div>
