@props([
    'damageLevel' => 'minimal',
    'count' => null,
    'variant' => 'pin',
])

@php
    $pinColors = match($damageLevel) {
        'minimal'  => 'bg-green-500 shadow-green-500/40',
        'partial'  => 'bg-amber-500 shadow-amber-500/40',
        'complete' => 'bg-red-600 shadow-red-600/40',
        default    => 'bg-slate-400 shadow-slate-400/40',
    };

    $clusterColors = match($damageLevel) {
        'minimal'  => 'bg-green-100 text-green-800 border-green-300',
        'partial'  => 'bg-amber-100 text-amber-800 border-amber-300',
        'complete' => 'bg-red-100 text-red-800 border-red-300',
        default    => 'bg-slate-100 text-slate-800 border-slate-300',
    };

    $damageLevelLabel = match($damageLevel) {
        'minimal'  => 'Minimal damage',
        'partial'  => 'Partial damage',
        'complete' => 'Complete destruction',
        default    => ucfirst($damageLevel),
    };
@endphp

@if($variant === 'cluster')
    <div
        {{ $attributes->class([
            'inline-flex items-center justify-center h-12 w-12 rounded-full border-2 font-heading font-semibold text-body-sm',
            $clusterColors,
        ]) }}
        role="img"
        aria-label="{{ $count }} reports with {{ $damageLevelLabel }}"
    >
        {{ $count }}
    </div>
@else
    <div
        {{ $attributes->class([
            'relative inline-flex items-center justify-center',
        ]) }}
        role="img"
        aria-label="Map pin: {{ $damageLevelLabel }}"
    >
        <span class="block h-4 w-4 rounded-full shadow-lg {{ $pinColors }}" aria-hidden="true"></span>
        <span class="absolute h-4 w-4 rounded-full animate-ping opacity-30 {{ $pinColors }}" aria-hidden="true"></span>
    </div>
@endif
