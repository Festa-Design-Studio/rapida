<?php

use App\Models\Crisis;
use Livewire\Component;

new class extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection<int, Crisis> */
    public $crises;

    public string $name = '';

    public string $slug = '';

    public string $defaultLanguage = 'en';

    public string $status = 'draft';

    /** @var array<int, string> */
    public array $activeModules = [];

    public ?string $editingId = null;

    public function mount(): void
    {
        $this->loadCrises();
    }

    public function loadCrises(): void
    {
        $this->crises = Crisis::withCount('damageReports')->latest()->get();
    }

    public function createCrisis(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:crises,slug',
            'defaultLanguage' => 'required|string|max:10',
            'status' => 'required|in:draft,active,archived',
        ]);

        Crisis::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'default_language' => $this->defaultLanguage,
            'active_modules' => $this->activeModules,
            'status' => $this->status,
            'available_languages' => ['en', 'fr', 'ar', 'es', 'ru', 'zh'],
        ]);

        $this->reset(['name', 'slug', 'defaultLanguage', 'status', 'activeModules']);
        $this->loadCrises();
    }

    public function updateCrisis(string $id): void
    {
        $crisis = Crisis::findOrFail($id);

        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:crises,slug,' . $id,
            'defaultLanguage' => 'required|string|max:10',
            'status' => 'required|in:draft,active,archived',
        ]);

        $crisis->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'default_language' => $this->defaultLanguage,
            'status' => $this->status,
        ]);

        $this->editingId = null;
        $this->reset(['name', 'slug', 'defaultLanguage', 'status']);
        $this->loadCrises();
    }

    public function editCrisis(string $id): void
    {
        $crisis = Crisis::findOrFail($id);
        $this->editingId = $id;
        $this->name = $crisis->name;
        $this->slug = $crisis->slug;
        $this->defaultLanguage = $crisis->default_language;
        $this->status = $crisis->status;
        $this->activeModules = $crisis->active_modules ?? [];
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->reset(['name', 'slug', 'defaultLanguage', 'status', 'activeModules']);
    }

    public function toggleStatus(string $id): void
    {
        $crisis = Crisis::findOrFail($id);
        $newStatus = $crisis->status === 'active' ? 'archived' : 'active';
        $crisis->update(['status' => $newStatus]);
        $this->loadCrises();
    }

    public function deleteCrisis(string $id): void
    {
        Crisis::findOrFail($id)->delete();
        $this->loadCrises();
    }
};
?>

<div class="space-y-6">
    {{-- Create / Edit form --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-h3 font-heading font-semibold text-slate-900 mb-4">
            {{ $editingId ? 'Edit Crisis' : 'Create New Crisis' }}
        </h2>
        <form wire:submit="{{ $editingId ? 'updateCrisis(\'' . $editingId . '\')' : 'createCrisis' }}" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-atoms.text-input
                    name="name"
                    label="Crisis name"
                    placeholder="e.g. Accra Urban Flood 2026"
                    :required="true"
                    :error="$errors->first('name')"
                    wire:model="name"
                />
                <x-atoms.text-input
                    name="slug"
                    label="Slug"
                    placeholder="e.g. accra-flood-2026"
                    :required="true"
                    :error="$errors->first('slug')"
                    wire:model="slug"
                />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-atoms.select
                    name="defaultLanguage"
                    label="Default language"
                    :options="['en' => 'English', 'fr' => 'French', 'ar' => 'Arabic', 'es' => 'Spanish', 'ru' => 'Russian', 'zh' => 'Chinese']"
                    wire:model="defaultLanguage"
                />
                <x-atoms.select
                    name="status"
                    label="Status"
                    :options="['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']"
                    wire:model="status"
                />
            </div>
            <div class="flex items-center gap-3">
                <x-atoms.button type="submit" variant="primary">
                    {{ $editingId ? 'Update Crisis' : 'Create Crisis' }}
                </x-atoms.button>
                @if($editingId)
                    <x-atoms.button type="button" variant="ghost" wire:click="cancelEdit">
                        Cancel
                    </x-atoms.button>
                @endif
            </div>
        </form>
    </div>

    {{-- Crises table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-h4 font-heading font-semibold text-slate-900">All Crises</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead class="bg-surface-page">
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 font-medium text-slate-600">Name</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Slug</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Status</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Language</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Reports</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Modules</th>
                        <th class="px-4 py-3 font-medium text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($crises as $crisis)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $crisis->name }}</td>
                            <td class="px-4 py-3 font-mono text-caption text-slate-500">{{ $crisis->slug }}</td>
                            <td class="px-4 py-3">
                                <x-atoms.badge variant="{{ match($crisis->status) {
                                    'active' => 'synced',
                                    'draft' => 'pending',
                                    'archived' => 'draft',
                                    default => 'default',
                                } }}">
                                    {{ ucfirst($crisis->status) }}
                                </x-atoms.badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ strtoupper($crisis->default_language) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $crisis->damage_reports_count }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ is_array($crisis->active_modules) ? count($crisis->active_modules) : 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <x-atoms.button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="editCrisis('{{ $crisis->id }}')"
                                    >
                                        Edit
                                    </x-atoms.button>
                                    <x-atoms.button
                                        variant="ghost"
                                        size="sm"
                                        wire:click="toggleStatus('{{ $crisis->id }}')"
                                    >
                                        {{ $crisis->status === 'active' ? 'Archive' : 'Activate' }}
                                    </x-atoms.button>
                                    <x-atoms.button
                                        variant="danger"
                                        size="sm"
                                        wire:click="deleteCrisis('{{ $crisis->id }}')"
                                        wire:confirm="Are you sure you want to delete this crisis? This cannot be undone."
                                    >
                                        Delete
                                    </x-atoms.button>
                                </div>
                            </td>
                        </tr>
                        {{-- WhatsApp QR Code --}}
                        @if($crisis->status === 'active' && ($crisis->whatsapp_enabled ?? true))
                            @php
                                $whatsappPhone = ltrim(config('services.twilio.whatsapp_from', 'whatsapp:+14155238886'), 'whatsapp:+');
                                $whatsappUrl = 'https://api.whatsapp.com/send?phone=' . $whatsappPhone . '&text=RAPIDA+' . $crisis->slug;
                                $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($whatsappUrl);
                            @endphp
                            <tr class="bg-slate-50">
                                <td colspan="7" class="px-4 pb-4 pt-0">
                                    <div class="mt-2 p-4 bg-white rounded-lg border border-slate-200">
                                        <p class="text-sm font-medium text-slate-700 mb-2">WhatsApp Report Link</p>
                                        <div class="flex items-start gap-4">
                                            <img src="{{ $qrUrl }}" alt="WhatsApp QR for {{ $crisis->name }}" width="100" height="100" class="rounded" loading="lazy" />
                                            <div class="text-xs text-slate-500 space-y-1">
                                                <p class="font-mono break-all">{{ $whatsappUrl }}</p>
                                                <a href="{{ $qrUrl }}" download="rapida-qr-{{ $crisis->slug }}.png"
                                                   class="inline-flex items-center text-rapida-blue-700 hover:underline">
                                                    Download QR
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">No crises configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
