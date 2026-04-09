@props([
    'items' => null,
])

@php
    $defaultItems = [
        ['color' => 'bg-damage-minimal-map', 'label' => __('rapida.damage_minimal')],
        ['color' => 'bg-damage-partial-map', 'label' => __('rapida.damage_partial')],
        ['color' => 'bg-damage-complete-map', 'label' => __('rapida.damage_complete')],
    ];

    $legendItems = $items ?? $defaultItems;
@endphp

<div {{ $attributes->class(['bg-white/90 backdrop-blur-sm rounded-lg p-3 shadow-sm']) }}>
    <p class="text-caption font-medium text-slate-700 mb-2">{{ __('rapida.damage_level_label') }}</p>
    <div class="space-y-1.5">
        @foreach($legendItems as $item)
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full {{ $item['color'] }} shrink-0" aria-hidden="true"></span>
                <span class="text-caption text-slate-600">{{ $item['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
