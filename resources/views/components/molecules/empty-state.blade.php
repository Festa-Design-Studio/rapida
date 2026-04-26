@props([
    'icon' => 'inbox',
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
    {{ $attributes->class([
        'flex flex-col items-center justify-center text-center gap-2',
        'px-4 py-12 text-slate-500',
    ]) }}
>
    <x-atoms.icon :name="$icon" size="lg" class="text-slate-300" />
    @if($title)
        <p class="text-body font-medium text-slate-700">{{ $title }}</p>
    @endif
    @if($body)
        <p class="text-body-sm text-slate-500">{{ $body }}</p>
    @endif
    @isset($slot)
        @if(trim($slot))
            <div class="mt-2">{{ $slot }}</div>
        @endif
    @endisset
</{!! $tag !!}>
