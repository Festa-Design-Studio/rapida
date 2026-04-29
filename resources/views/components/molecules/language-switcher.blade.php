@props([
    'current' => 'en',
    'languages' => [],
    'variant' => 'dropdown',
])

<div
    x-data="{
        switchLang(code) {
            if (code === '{{ $current }}') return;
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
    {{ $attributes }}
>
    @if($variant === 'dropdown')
        {{-- Compact dropdown — fits 6 languages in minimal space --}}
        <div class="relative">
            <select
                @change="switchLang($event.target.value)"
                aria-label="Language"
                class="appearance-none bg-rapida-blue-700 text-white text-body-sm font-heading font-semibold
                       rounded-lg ps-inner pe-component py-micro min-h-touch-min cursor-pointer
                       border border-transparent
                       focus:outline-none focus:ring-2 focus:ring-rapida-blue-500 focus:ring-offset-2
                       transition-colors duration-fast"
            >
                @foreach($languages as $code => $name)
                    <option value="{{ $code }}" {{ $code === $current ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 end-0 flex items-center pe-micro pointer-events-none">
                <svg class="h-inner w-inner text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    @else
        {{-- Badge variant for onboarding page (more space available) --}}
        <div class="flex flex-wrap gap-2" role="radiogroup" aria-label="Language selection">
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
                               cursor-pointer transition-colors duration-fast min-h-touch-min min-w-touch-min flex items-center justify-center"
                    >
                        {{ strtoupper($code) }}
                    </x-atoms.badge>
                </label>
            @endforeach
        </div>
    @endif
</div>
