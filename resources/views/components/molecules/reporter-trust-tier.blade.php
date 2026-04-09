@props([
    'tier' => 'anonymous',
    'isTrusted' => false,
])

@php
    $display = match(true) {
        $isTrusted => ['label' => __('rapida.trusted_contributor'), 'variant' => 'verified'],
        $tier === 'account' => ['label' => __('account.verified', ['default' => 'Verified']), 'variant' => 'info'],
        $tier === 'device' => ['label' => __('account.contributor', ['default' => 'Contributor']), 'variant' => 'default'],
        default => null,
    };
@endphp

@if($display)
    <x-atoms.badge :variant="$display['variant']">
        {{ $display['label'] }}
    </x-atoms.badge>
@endif
