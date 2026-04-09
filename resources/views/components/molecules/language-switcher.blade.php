@props([
    'current' => 'en',
    'languages' => [],
])

<div
    {{ $attributes->class(['flex flex-wrap gap-2']) }}
    role="radiogroup"
    aria-label="Language selection"
>
    @foreach($languages as $code => $name)
        <label class="cursor-pointer">
            <input
                type="radio"
                name="language"
                value="{{ $code }}"
                class="sr-only peer"
                @if($code === $current) checked @endif
                aria-label="{{ $name }}"
            />
            <x-atoms.badge
                variant="language"
                class="peer-checked:bg-rapida-blue-700 peer-checked:text-white peer-checked:border-transparent
                       peer-focus-visible:ring-2 peer-focus-visible:ring-rapida-blue-700 peer-focus-visible:ring-offset-2
                       cursor-pointer transition-colors duration-150 min-h-[48px] min-w-[48px] flex items-center justify-center"
            >
                {{ strtoupper($code) }}
            </x-atoms.badge>
        </label>
    @endforeach
</div>
