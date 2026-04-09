@props([
    'name' => 'damage_level',
    'value' => null,
    'required' => false,
    'error' => null,
])

@php
    $options = [
        'minimal' => [
            'label' => 'Minimal / No Damage',
            'description' => 'Superficial damage; building is safe and functional.',
            'color' => 'bg-damage-minimal-map',
        ],
        'partial' => [
            'label' => 'Partial Damage',
            'description' => 'Structural issues; building may be unsafe to occupy.',
            'color' => 'bg-damage-partial-map',
        ],
        'complete' => [
            'label' => 'Complete Destruction',
            'description' => 'Total structural failure; building is uninhabitable.',
            'color' => 'bg-damage-complete-map',
        ],
    ];
@endphp

<x-atoms.radio-group
    :name="$name"
    :value="$value"
    :required="$required"
    :error="$error"
    :options="$options"
    variant="card"
    legend="Damage Classification"
/>
