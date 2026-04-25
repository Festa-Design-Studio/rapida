@props([
    'name',
    'legend' => null,
    'required' => false,
    'error' => null,
    'options' => [],
    'value' => null,
    'variant' => 'standard',
])

@php
    $errorId = $error ? "{$name}-error" : null;
@endphp

<fieldset class="flex flex-col gap-3" @if($required) aria-required="true" @endif>
    @if($legend)
        <legend class="text-label font-medium text-slate-700 mb-1">
            {{ $legend }}
            @if($required)
                <span class="text-crisis-rose-700 ml-0.5" aria-hidden="true">*</span>
            @endif
        </legend>
    @endif

    {{ $slot }}

    @forelse($options as $optionValue => $option)
        @php
            $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
            $optionDesc = is_array($option) ? ($option['description'] ?? null) : null;
            $optionColor = is_array($option) ? ($option['color'] ?? null) : null;
            $isChecked = (string) $value === (string) $optionValue;
        @endphp

        @if($variant === 'card')
            <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-slate-200
                        cursor-pointer hover:border-rapida-blue-500 hover:bg-rapida-blue-50/50
                        has-[:checked]:border-rapida-blue-700 has-[:checked]:bg-rapida-blue-50
                        transition-all duration-150">
                <input type="radio" name="{{ $name }}" value="{{ $optionValue }}"
                       class="sr-only"
                       @if($isChecked) checked @endif
                       aria-label="{{ $optionLabel }}" />
                @if($optionColor)
                    <div class="w-4 h-4 rounded-full shrink-0 {{ $optionColor }}" aria-hidden="true"></div>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-body font-medium text-slate-900">{{ $optionLabel }}</p>
                    @if($optionDesc)
                        <p class="text-body-sm text-slate-500">{{ $optionDesc }}</p>
                    @endif
                </div>
            </label>
        @else
            <label class="flex items-center gap-3 h-12 px-4 rounded-lg border border-slate-300
                        cursor-pointer hover:border-rapida-blue-500 hover:bg-rapida-blue-50/50
                        has-[:checked]:border-rapida-blue-700 has-[:checked]:bg-rapida-blue-50
                        transition-colors duration-150">
                <input type="radio" name="{{ $name }}" value="{{ $optionValue }}"
                       class="h-5 w-5 accent-rapida-blue-700 focus:ring-2 focus:ring-rapida-blue-700"
                       @if($isChecked) checked @endif />
                <span class="text-body text-slate-900">{{ $optionLabel }}</span>
            </label>
        @endif
    @empty
    @endforelse

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-crisis-rose-700 flex items-center gap-1">
            {{ $error }}
        </p>
    @endif
</fieldset>
