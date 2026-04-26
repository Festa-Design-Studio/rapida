<?php

use App\Models\Crisis;
use App\Models\Landmark;
use Livewire\Component;

new class extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, Landmark> */
    public $landmarks;

    /** @var \Illuminate\Database\Eloquent\Collection<int, Crisis> */
    public $crises;

    public string $name = '';

    public string $type = '';

    public string $crisisId = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public function mount(): void
    {
        $this->loadLandmarks();
        $this->crises = Crisis::orderBy('name')->get();
    }

    public function loadLandmarks(): void
    {
        $this->landmarks = Landmark::with('crisis')->latest()->get();
    }

    public function createLandmark(): void
    {
        $this->authorize('create', Landmark::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'crisisId' => 'required|exists:crises,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Landmark::create([
            'name' => $this->name,
            'type' => $this->type,
            'crisis_id' => $this->crisisId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'added_by' => auth('undp')->id(),
        ]);

        $this->reset(['name', 'type', 'crisisId', 'latitude', 'longitude']);
        $this->loadLandmarks();
    }

    public function deleteLandmark(string $id): void
    {
        $landmark = Landmark::findOrFail($id);
        $this->authorize('delete', $landmark);
        $landmark->delete();
        $this->loadLandmarks();
    }
};
?>

<div class="space-y-6">
    {{-- Create form --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-h3 font-heading font-semibold text-slate-900 mb-4">Add Landmark</h2>
        <form wire:submit="createLandmark" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-atoms.text-input
                    name="name"
                    label="Landmark name"
                    placeholder="e.g. Central Hospital"
                    :required="true"
                    :error="$errors->first('name')"
                    wire:model="name"
                />
                <x-atoms.select
                    name="type"
                    label="Type"
                    placeholder="Select type..."
                    :required="true"
                    :options="[
                        'hospital' => 'Hospital',
                        'school' => 'School',
                        'mosque' => 'Mosque',
                        'church' => 'Church',
                        'market' => 'Market',
                        'government' => 'Government Building',
                        'bridge' => 'Bridge',
                        'water_source' => 'Water Source',
                        'shelter' => 'Shelter',
                        'other' => 'Other',
                    ]"
                    :error="$errors->first('type')"
                    wire:model="type"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-atoms.select
                    name="crisisId"
                    label="Crisis"
                    placeholder="Select crisis..."
                    :required="true"
                    :options="$crises->pluck('name', 'id')->all()"
                    :error="$errors->first('crisisId')"
                    wire:model="crisisId"
                />
                <x-atoms.text-input
                    name="latitude"
                    label="Latitude"
                    type="number"
                    placeholder="e.g. 5.5560"
                    :required="true"
                    :error="$errors->first('latitude')"
                    wire:model="latitude"
                />
                <x-atoms.text-input
                    name="longitude"
                    label="Longitude"
                    type="number"
                    placeholder="e.g. -0.1969"
                    :required="true"
                    :error="$errors->first('longitude')"
                    wire:model="longitude"
                />
            </div>
            <x-atoms.button type="submit" variant="primary">
                Add Landmark
            </x-atoms.button>
        </form>
    </div>

    {{-- Landmarks table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-h4 font-heading font-semibold text-slate-900">All Landmarks</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead class="bg-surface-page">
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 font-medium text-slate-600">Name</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Type</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Crisis</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Coordinates</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($landmarks as $landmark)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $landmark->name }}</td>
                            <td class="px-4 py-3">
                                <x-atoms.badge variant="info">
                                    {{ ucfirst(str_replace('_', ' ', $landmark->type)) }}
                                </x-atoms.badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $landmark->crisis?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-mono text-caption text-slate-500">{{ $landmark->latitude }}, {{ $landmark->longitude }}</td>
                            <td class="px-4 py-3">
                                <x-atoms.button
                                    variant="danger"
                                    size="sm"
                                    wire:click="deleteLandmark('{{ $landmark->id }}')"
                                    wire:confirm="Are you sure you want to delete this landmark?"
                                >
                                    Delete
                                </x-atoms.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <x-molecules.empty-state
                                icon="map-pin"
                                title="No landmarks added yet"
                                body="Add reference locations operators can pre-fill in the wizard."
                                colspan="5"
                            />
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
