@props([
    'title' => null,
    'body' => null,
    'colspan' => null,  // when used inside a <td>, set colspan to span the row
])

@php
    $tag = $colspan !== null ? 'td' : 'div';
    $tdAttr = $colspan !== null ? "colspan=\"{$colspan}\"" : '';
@endphp

<{!! $tag !!}
    {!! $tdAttr !!}
    role="alert"
    aria-live="polite"
    {{ $attributes->class([
        'flex flex-col items-center justify-center text-center gap-2',
        'px-4 py-8 rounded-lg bg-crisis-rose-50 border border-crisis-rose-100 text-crisis-rose-900',
    ]) }}
>
    <x-atoms.icon name="alert-triangle" size="md" class="text-crisis-rose-700" />
    @if($title)
        <p class="text-body font-medium">{{ $title }}</p>
    @endif
    @if($body)
        <p class="text-body-sm text-text-primary">{{ $body }}</p>
    @endif
    @isset($slot)
        @if(trim($slot))
            <div class="mt-2">{{ $slot }}</div>
        @endif
    @endisset
</{!! $tag !!}>
