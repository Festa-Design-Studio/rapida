<?php

use Livewire\Component;

new class extends Component
{
    public string $format = 'csv';

    public ?string $damageFilter = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function export(): mixed
    {
        $params = array_filter([
            'damage_level' => $this->damageFilter,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);

        $route = match ($this->format) {
            'geojson' => route('export.geojson', $params),
            'kml' => route('export.kml', $params),
            'shapefile' => route('export.shapefile', $params),
            'pdf' => route('export.pdf', $params),
            default => route('export.csv', $params),
        };

        return $this->redirect($route);
    }
};
?>

<div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <h3 class="text-h4 font-semibold font-heading text-slate-900 mb-4">Export Data</h3>

    <form wire:submit="export" class="space-y-4">
        {{-- Format --}}
        <x-atoms.select name="format" label="Format" wire:model="format">
            <option value="csv">CSV</option>
            <option value="geojson">GeoJSON</option>
            <option value="kml">KML</option>
            <option value="shapefile">Shapefile (ZIP)</option>
            <option value="pdf">PDF Summary</option>
        </x-atoms.select>

        {{-- Damage level filter --}}
        <x-atoms.select name="damageFilter" label="Damage Level" wire:model="damageFilter">
            <option value="">All Levels</option>
            <option value="minimal">Minimal</option>
            <option value="partial">Partial</option>
            <option value="complete">Complete</option>
        </x-atoms.select>

        {{-- Date range --}}
        <div class="grid grid-cols-2 gap-3">
            <x-atoms.text-input type="date" name="startDate" label="Start Date" wire:model="startDate" />
            <x-atoms.text-input type="date" name="endDate" label="End Date" wire:model="endDate" />
        </div>

        {{-- Export button --}}
        <x-atoms.button type="submit" variant="primary" class="w-full min-h-[48px]">
            Export Reports
        </x-atoms.button>
    </form>
</div>
