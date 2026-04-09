@props([
    'name',
    'value' => null,
    'label' => null,
    'description' => null,
    'checked' => false,
    'required' => false,
    'disabled' => false,
])

@php
    $inputId = $attributes->get('id', $name . ($value ? "-{$value}" : ''));
@endphp

<label class="flex items-start gap-3 px-4 py-3 rounded-lg border border-slate-200
            cursor-pointer hover:border-rapida-blue-500 hover:bg-rapida-blue-50/50
            has-[:checked]:border-rapida-blue-700 has-[:checked]:bg-rapida-blue-50
            transition-colors duration-150
            {{ $disabled ? 'opacity-40 cursor-not-allowed pointer-events-none' : '' }}">
    <input
        type="checkbox"
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if($value) value="{{ $value }}" @endif
        @if($checked) checked @endif
        @if($required) required aria-required="true" @endif
        @if($disabled) disabled @endif
        class="mt-0.5 h-5 w-5 rounded accent-rapida-blue-700
               focus:ring-2 focus:ring-rapida-blue-700 focus:ring-offset-1 shrink-0"
    />
    <div>
        @if($label)
            <p class="text-body text-slate-900">{{ $label }}</p>
        @endif
        @if($description)
            <p class="text-body-sm text-slate-500">{{ $description }}</p>
        @endif
        {{ $slot }}
    </div>
</label>
