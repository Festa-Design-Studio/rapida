@props([
    'name' => 'infrastructure_type',
    'values' => [],
    'required' => false,
    'error' => null,
])

@php
    $types = [
        'residential' => [
            'label' => 'Residential',
            'description' => 'Houses, apartments, shelters',
        ],
        'commercial' => [
            'label' => 'Commercial',
            'description' => 'Shops, markets, offices',
        ],
        'hospital' => [
            'label' => 'Hospital / Health',
            'description' => 'Clinics, hospitals, pharmacies',
        ],
        'school' => [
            'label' => 'School / Education',
            'description' => 'Schools, universities, training centers',
        ],
        'road' => [
            'label' => 'Road / Bridge',
            'description' => 'Roads, bridges, transport infrastructure',
        ],
        'utility' => [
            'label' => 'Utility / Power',
            'description' => 'Power lines, water systems, telecom',
        ],
        'government' => [
            'label' => 'Government',
            'description' => 'Government buildings, public services',
        ],
        'community' => [
            'label' => 'Community / Religious',
            'description' => 'Mosques, churches, community centers',
        ],
    ];

    $errorId = $error ? "{$name}-error" : null;
@endphp

<fieldset class="flex flex-col gap-3" @if($required) aria-required="true" @endif>
    <legend class="text-label font-medium text-slate-700 mb-1">
        Infrastructure Type
        @if($required)
            <span class="text-red-600 ml-0.5" aria-hidden="true">*</span>
        @endif
    </legend>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($types as $typeValue => $type)
            <x-atoms.checkbox
                name="{{ $name }}[]"
                value="{{ $typeValue }}"
                :label="$type['label']"
                :description="$type['description']"
                :checked="in_array($typeValue, $values)"
            />
        @endforeach
    </div>

    @if($error)
        <p id="{{ $errorId }}" role="alert" class="text-body-sm text-red-700 flex items-center gap-1">
            {{ $error }}
        </p>
    @endif
</fieldset>
