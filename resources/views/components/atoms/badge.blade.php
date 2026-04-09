@props([
    'variant' => 'default',
    'size' => 'default',
    'dot' => null,
])

@php
    $variantClasses = match($variant) {
        'minimal'   => 'bg-damage-minimal-ui-surface text-damage-minimal-ui-text border-damage-minimal-ui-border',
        'partial'   => 'bg-damage-partial-ui-surface text-damage-partial-ui-text border-damage-partial-ui-border',
        'complete'  => 'bg-damage-complete-ui-surface text-damage-complete-ui-text border-damage-complete-ui-border',
        'synced'    => 'bg-ground-green-50 text-ground-green-900 border-ground-green-200',
        'pending'   => 'bg-alert-amber-50 text-alert-amber-900 border-alert-amber-100',
        'failed'    => 'bg-crisis-rose-50 text-crisis-rose-900 border-crisis-rose-100',
        'draft'     => 'bg-slate-100 text-slate-600 border-slate-200',
        'verified'  => 'bg-rapida-blue-700 text-white border-transparent',
        'info'      => 'bg-rapida-blue-50 text-rapida-blue-900 border-rapida-blue-100',
        'language'  => 'bg-slate-100 text-slate-700 border-slate-200',
        'confidence-high'   => 'bg-ground-green-50 text-ground-green-900 border-ground-green-200',
        'confidence-medium' => 'bg-alert-amber-50 text-alert-amber-900 border-alert-amber-100',
        'confidence-low'    => 'bg-crisis-rose-50 text-crisis-rose-900 border-crisis-rose-100',
        default     => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $dotColor = match($variant) {
        'minimal'  => 'bg-damage-minimal-map',
        'partial'  => 'bg-damage-partial-map',
        'complete' => 'bg-damage-complete-map',
        default    => null,
    };

    $sizeClasses = match($size) {
        'lg'    => 'px-3 py-1.5 text-body-sm',
        default => 'px-2.5 py-1 text-caption',
    };
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 font-medium rounded-full border',
    $variantClasses,
    $sizeClasses,
]) }}>
    @if($dot ?? $dotColor)
        <span class="h-2 w-2 rounded-full shrink-0 {{ $dotColor ?? $dot }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
