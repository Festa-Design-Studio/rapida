@props([
    'name' => 'crisis_type',
    'value' => null,
    'required' => false,
    'error' => null,
])

@php
    $options = [
        'natural' => [
            'label' => 'Natural Disaster',
            'description' => 'Earthquake, flood, hurricane, wildfire, landslide, or other natural event.',
        ],
        'technological' => [
            'label' => 'Technological / Industrial',
            'description' => 'Chemical spill, industrial accident, infrastructure collapse, or power failure.',
        ],
        'human-made' => [
            'label' => 'Human-made / Conflict',
            'description' => 'Armed conflict, civil unrest, terrorism, or deliberate destruction.',
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
    legend="Crisis Type"
/>
