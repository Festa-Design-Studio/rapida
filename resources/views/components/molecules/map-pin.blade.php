@props([
    'damageLevel' => 'minimal',
    'count' => null,
    'variant' => 'pin',
])

@php
    $pinColors = match($damageLevel) {
        'minimal'  => 'bg-damage-minimal-map shadow-ground-green-500/40',
        'partial'  => 'bg-damage-partial-map shadow-alert-amber-500/40',
        'complete' => 'bg-damage-complete-map shadow-crisis-rose-400/40',
        default    => 'bg-slate-400 shadow-slate-400/40',
    };

    $clusterColors = match($damageLevel) {
        'minimal'  => 'bg-damage-minimal-ui-surface text-damage-minimal-ui-text border-damage-minimal-ui-border',
        'partial'  => 'bg-damage-partial-ui-surface text-damage-partial-ui-text border-damage-partial-ui-border',
        'complete' => 'bg-damage-complete-ui-surface text-damage-complete-ui-text border-damage-complete-ui-border',
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
