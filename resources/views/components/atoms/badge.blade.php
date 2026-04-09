@props([
    'variant' => 'default',
    'size' => 'default',
    'dot' => null,
])

@php
    $variantClasses = match($variant) {
        'minimal'   => 'bg-green-100 text-green-800 border-green-200',
        'partial'   => 'bg-amber-100 text-amber-800 border-amber-200',
        'complete'  => 'bg-red-100 text-red-800 border-red-200',
        'synced'    => 'bg-green-100 text-green-800 border-green-200',
        'pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
        'failed'    => 'bg-red-100 text-red-800 border-red-200',
        'draft'     => 'bg-slate-100 text-slate-600 border-slate-200',
        'verified'  => 'bg-rapida-blue-700 text-white border-transparent',
        'info'      => 'bg-rapida-blue-50 text-rapida-blue-900 border-rapida-blue-100',
        'language'  => 'bg-slate-100 text-slate-700 border-slate-200',
        default     => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $dotColor = match($variant) {
        'minimal'  => 'bg-green-500',
        'partial'  => 'bg-amber-500',
        'complete' => 'bg-red-600',
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
