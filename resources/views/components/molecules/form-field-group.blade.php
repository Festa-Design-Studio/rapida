@props([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
    'error' => null,
    'optional' => false,
])

@php
    $inputId = $name;
    $helpId = $help ? "{$inputId}-help" : null;
    $errorId = $error ? "{$inputId}-error" : null;
    $describedBy = collect([$errorId, $helpId])->filter()->implode(' ') ?: null;
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-label font-medium text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-crisis-rose-700 ml-0.5" aria-hidden="true">*</span>
            @endif
            @if($optional)
                <span class="text-slate-400 text-caption font-normal ml-1">(optional)</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-crisis-rose-700 flex items-center gap-1">
            {{ $error }}
        </p>
    @elseif($help)
        <p id="{{ $helpId }}" class="text-body-sm text-slate-500" aria-describedby="{{ $describedBy }}">
            {{ $help }}
        </p>
    @endif
</div>
