@props([
    'formats' => ['csv', 'geojson', 'pdf'],
])

@php
    $formatLabels = [
        'csv' => 'CSV (Spreadsheet)',
        'geojson' => 'GeoJSON (Map Data)',
        'pdf' => 'PDF (Report)',
    ];

    $exportFields = [
        'location' => 'Location / Address',
        'damage_level' => 'Damage Level',
        'infrastructure_type' => 'Infrastructure Type',
        'crisis_type' => 'Crisis Type',
        'description' => 'Description',
        'photo' => 'Photos',
        'reporter' => 'Reporter Name',
        'submitted_at' => 'Submission Date',
        'coordinates' => 'GPS Coordinates',
        'sync_status' => 'Sync Status',
    ];

    $damageLevels = [
        'all' => 'All Levels',
        'minimal' => 'Minimal',
        'partial' => 'Partial',
        'complete' => 'Complete',
    ];

    $formatOptions = [];
    foreach ($formats as $format) {
        $formatOptions[$format] = $formatLabels[$format] ?? strtoupper($format);
    }
@endphp

<div
    x-data="{ exporting: false }"
    {{ $attributes->class(['w-full rounded-xl border border-slate-200 bg-white p-6 space-y-6']) }}
    role="region"
    aria-label="Data export interface"
>
    <div class="flex items-center justify-between">
        <h2 class="text-h3 font-heading font-semibold text-slate-900">Export Data</h2>
        <x-atoms.badge variant="info">UNDP Coordinators</x-atoms.badge>
    </div>

    {{-- Export format --}}
    <x-molecules.form-field-group name="export_format" label="Export Format" required>
        <x-atoms.select
            name="export_format"
            :options="$formatOptions"
            placeholder="Choose format..."
            required
        />
    </x-molecules.form-field-group>

    {{-- Date range --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-molecules.form-field-group name="date_from" label="From Date">
            <x-atoms.text-input name="date_from" type="date" />
        </x-molecules.form-field-group>
        <x-molecules.form-field-group name="date_to" label="To Date">
            <x-atoms.text-input name="date_to" type="date" />
        </x-molecules.form-field-group>
    </div>

    {{-- Damage level filter --}}
    <x-molecules.form-field-group name="damage_filter" label="Damage Level Filter">
        <x-atoms.select
            name="damage_filter"
            :options="$damageLevels"
            value="all"
        />
    </x-molecules.form-field-group>

    {{-- Field selection --}}
    <fieldset class="space-y-3" aria-label="Select fields to export">
        <legend class="text-label font-medium text-slate-700 mb-2">Fields to Include</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($exportFields as $fieldValue => $fieldLabel)
                <x-atoms.checkbox
                    name="export_fields[]"
                    value="{{ $fieldValue }}"
                    label="{{ $fieldLabel }}"
                    :checked="true"
                />
            @endforeach
        </div>
    </fieldset>

    {{-- Status notification --}}
    <div x-show="exporting" x-transition>
        <x-molecules.notification type="info" message="Preparing your export. This may take a moment..." />
    </div>

    {{-- Export button --}}
    <div class="flex items-center gap-3 pt-2">
        <x-atoms.button
            variant="primary"
            size="lg"
            @click="exporting = true; setTimeout(() => exporting = false, 3000)"
            aria-label="Start data export"
        >
            <x-atoms.icon name="download" size="sm" />
            Export Data
        </x-atoms.button>
        <x-atoms.button variant="ghost" size="md">
            Reset Filters
        </x-atoms.button>
    </div>
</div>
