@props([
    'current' => 'en',
    'languages' => [],
])

<div
    x-data="{
        switchLang(code) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('onboarding.language') }}';
            const token = document.createElement('input');
            token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
            const lang = document.createElement('input');
            lang.type = 'hidden'; lang.name = 'language'; lang.value = code;
            form.appendChild(token);
            form.appendChild(lang);
            document.body.appendChild(form);
            form.submit();
        }
    }"
    {{ $attributes->class(['flex flex-wrap gap-2']) }}
    role="radiogroup"
    aria-label="Language selection"
>
    @foreach($languages as $code => $name)
        <label class="cursor-pointer" @click.prevent="switchLang('{{ $code }}')">
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
