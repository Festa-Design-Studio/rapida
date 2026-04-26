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
    public array $availableLanguages = ['en', 'fr', 'ar', 'es', 'ru', 'zh'];

    /** @var array<int, string> */
    public array $activeModules = [];

    public bool $conflictContext = false;

    public bool $confirmConflictContext = false;

    public string $crisisTypeDefault = 'flood';

    public bool $multiPhotoEnabled = false;

    public int $multiPhotoMax = 5;

    public bool $dangerZonesEnabled = false;

    public int $dataRetentionDays = 365;

    public int $h3Resolution = 9;

    public ?string $editingId = null;

    public function mount(): void
    {
        $this->loadCrises();
    }

    public function loadCrises(): void
    {
        $this->crises = Crisis::withCount('damageReports')->latest()->get();
    }

    /**
     * Operator clicked the conflict_context checkbox to enable. Show
     * the privacy-implications modal — actually persisting the flag
     * waits until they confirm via confirmConflictContextChange().
     */
    public function requestConflictContextEnable(): void
    {
        $this->confirmConflictContext = true;
    }

    public function cancelConflictContext(): void
    {
        $this->conflictContext = false;
        $this->confirmConflictContext = false;
    }

    public function confirmConflictContextChange(): void
    {
        $this->conflictContext = true;
        $this->confirmConflictContext = false;
    }

    public function createCrisis(): void
    {
        $this->authorize('create', Crisis::class);

        $this->validate($this->rules());

        Crisis::create($this->payload());

        $this->resetForm();
        $this->loadCrises();
    }

    public function updateCrisis(string $id): void
    {
        $crisis = Crisis::findOrFail($id);
        $this->authorize('update', $crisis);

        $this->validate($this->rules($id));

        $crisis->update($this->payload());

        $this->editingId = null;
        $this->resetForm();
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
        $this->availableLanguages = $crisis->available_languages ?? ['en'];
        $this->activeModules = $crisis->active_modules ?? [];
        $this->conflictContext = (bool) $crisis->conflict_context;
        $this->crisisTypeDefault = $crisis->crisis_type_default ?? 'flood';
        $this->multiPhotoEnabled = (bool) $crisis->multi_photo_enabled;
        $this->multiPhotoMax = $crisis->multi_photo_max ?? 5;
        $this->dangerZonesEnabled = (bool) $crisis->danger_zones_enabled;
        $this->dataRetentionDays = $crisis->data_retention_days ?? 365;
        $this->h3Resolution = $crisis->h3_resolution ?? 9;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->resetForm();
    }

    public function toggleStatus(string $id): void
    {
        $crisis = Crisis::findOrFail($id);
        $this->authorize('update', $crisis);
        $newStatus = $crisis->status === 'active' ? 'archived' : 'active';
        $crisis->update(['status' => $newStatus]);
        $this->loadCrises();
    }

    public function deleteCrisis(string $id): void
    {
        $crisis = Crisis::findOrFail($id);
        $this->authorize('delete', $crisis);
        $crisis->delete();
        $this->loadCrises();
    }

    /** @return array<string, string|array<int, string>> */
    private function rules(?string $id = null): array
    {
        $slugUnique = 'unique:crises,slug'.($id ? ','.$id : '');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|'.$slugUnique,
            'defaultLanguage' => 'required|string|max:10',
            'status' => 'required|in:draft,active,archived',
            'availableLanguages' => 'array|min:1',
            'availableLanguages.*' => 'string|in:en,fr,ar,es,ru,zh',
            'crisisTypeDefault' => 'required|in:flood,earthquake,cyclone,fire,conflict,other',
            'multiPhotoMax' => 'integer|min:1|max:10',
            'dataRetentionDays' => 'integer|min:30|max:3650',
            'h3Resolution' => 'integer|in:7,8,9,10',
        ];
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'default_language' => $this->defaultLanguage,
            'available_languages' => $this->availableLanguages,
            'active_modules' => $this->activeModules,
            'status' => $this->status,
            'conflict_context' => $this->conflictContext,
            'crisis_type_default' => $this->crisisTypeDefault,
            'multi_photo_enabled' => $this->multiPhotoEnabled,
            'multi_photo_max' => $this->multiPhotoMax,
            'danger_zones_enabled' => $this->dangerZonesEnabled,
            'data_retention_days' => $this->dataRetentionDays,
            'h3_resolution' => $this->h3Resolution,
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'name', 'slug', 'defaultLanguage', 'status', 'activeModules',
            'conflictContext', 'confirmConflictContext', 'crisisTypeDefault',
            'multiPhotoEnabled', 'multiPhotoMax', 'dangerZonesEnabled',
            'dataRetentionDays', 'h3Resolution',
        ]);
        $this->availableLanguages = ['en', 'fr', 'ar', 'es', 'ru', 'zh'];
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
            {{-- Identity --}}
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

            {{-- Localisation --}}
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

            {{-- Available languages (multi) --}}
            <fieldset class="border border-slate-200 rounded-lg p-4">
                <legend class="text-label font-medium text-slate-700 px-2">Available languages</legend>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mt-2">
                    @foreach(['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR', 'es' => 'ES', 'ru' => 'RU', 'zh' => 'ZH'] as $code => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                value="{{ $code }}"
                                wire:model="availableLanguages"
                                class="rounded border-slate-300 text-rapida-blue-600 focus:ring-rapida-blue-500"
                            />
                            <span class="text-body-sm text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('availableLanguages')
                    <p class="text-body-sm text-crisis-rose-600 mt-2" role="alert">{{ $message }}</p>
                @enderror
            </fieldset>

            {{-- Crisis type + spatial defaults --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-atoms.select
                    name="crisisTypeDefault"
                    label="Default crisis type"
                    :options="[
                        'flood' => 'Flood',
                        'earthquake' => 'Earthquake',
                        'cyclone' => 'Cyclone',
                        'fire' => 'Fire',
                        'conflict' => 'Conflict',
                        'other' => 'Other',
                    ]"
                    wire:model="crisisTypeDefault"
                />
                <x-atoms.select
                    name="h3Resolution"
                    label="H3 resolution"
                    :options="[7 => '7 (~5km)', 8 => '8 (~1km)', 9 => '9 (~150m)', 10 => '10 (~60m)']"
                    wire:model="h3Resolution"
                />
                <x-atoms.text-input
                    name="dataRetentionDays"
                    label="Data retention (days)"
                    type="number"
                    placeholder="365"
                    :error="$errors->first('dataRetentionDays')"
                    wire:model="dataRetentionDays"
                />
            </div>

            {{-- Privacy / safety toggles --}}
            <fieldset class="border border-slate-200 rounded-lg p-4 space-y-3">
                <legend class="text-label font-medium text-slate-700 px-2">Privacy &amp; safety</legend>

                {{-- Conflict context (gated by modal) --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        :checked="$conflictContext"
                        wire:click="requestConflictContextEnable"
                        @if($conflictContext) checked @endif
                        class="mt-1 rounded border-slate-300 text-crisis-rose-600 focus:ring-crisis-rose-500"
                    />
                    <div>
                        <span class="text-body-sm font-medium text-slate-900">Conflict context</span>
                        <p class="text-caption text-slate-500">
                            Enables anonymity-by-default, disables fingerprinting and
                            badges, and tightens the WhatsApp location prompt. Required
                            for active conflict zones.
                        </p>
                    </div>
                </label>

                {{-- Danger zones --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model="dangerZonesEnabled"
                        class="mt-1 rounded border-slate-300 text-rapida-blue-600 focus:ring-rapida-blue-500"
                    />
                    <div>
                        <span class="text-body-sm font-medium text-slate-900">Danger-zone alerts</span>
                        <p class="text-caption text-slate-500">
                            Surface H3-cell danger flags to incoming reporters in the
                            map. Disabled in conflict mode regardless of this setting.
                        </p>
                    </div>
                </label>

                {{-- Multi-photo --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="multiPhotoEnabled"
                        class="mt-1 rounded border-slate-300 text-rapida-blue-600 focus:ring-rapida-blue-500"
                    />
                    <div class="flex-1">
                        <span class="text-body-sm font-medium text-slate-900">Multi-photo per report</span>
                        <p class="text-caption text-slate-500">
                            Allow reporters to attach more than one photo per submission.
                        </p>
                    </div>
                </label>

                @if($multiPhotoEnabled)
                    <div class="ms-7 max-w-xs">
                        <x-atoms.text-input
                            name="multiPhotoMax"
                            label="Max photos per report"
                            type="number"
                            placeholder="5"
                            :error="$errors->first('multiPhotoMax')"
                            wire:model="multiPhotoMax"
                        />
                    </div>
                @endif
            </fieldset>

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

    {{-- Conflict-context confirmation modal --}}
    @if($confirmConflictContext)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="conflict-modal-title"
            x-data
            x-on:keydown.escape.window="$wire.cancelConflictContext()"
        >
            <div class="rounded-xl bg-white max-w-lg w-full p-6 shadow-2xl space-y-4">
                <h3 id="conflict-modal-title" class="text-h3 font-heading font-semibold text-crisis-rose-900">
                    Enable conflict context?
                </h3>
                <div class="space-y-3 text-body-sm text-slate-700">
                    <p>Conflict context changes how RAPIDA handles every report from this crisis:</p>
                    <ul class="list-disc ms-6 space-y-1">
                        <li><strong>No device fingerprints</strong> — reports cannot be linked back to a phone or repeat user.</li>
                        <li><strong>No GPS prompt on WhatsApp</strong> — reporters describe location via landmark, street, or what3words.</li>
                        <li><strong>No badges or leaderboards</strong> — anything that could identify a contributor over time is disabled.</li>
                        <li><strong>No AI dispatch</strong> — photos are not sent to remote classification services.</li>
                    </ul>
                    <p>This is the right setting for active conflict zones, surveillance-heavy contexts, or any deployment where reporter anonymity protects safety. <strong>Once you enable it, treat it as default-on for the duration of the crisis.</strong></p>
                </div>
                <div class="flex items-center gap-3 justify-end pt-2">
                    <x-atoms.button type="button" variant="ghost" wire:click="cancelConflictContext">
                        Cancel
                    </x-atoms.button>
                    <x-atoms.button type="button" variant="primary" wire:click="confirmConflictContextChange">
                        Yes, enable conflict context
                    </x-atoms.button>
                </div>
            </div>
        </div>
    @endif

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
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $crisis->name }}
                                @if($crisis->conflict_context)
                                    <x-atoms.badge variant="error" class="ms-2">Conflict</x-atoms.badge>
                                @endif
                            </td>
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
                                        @if(config('services.twilio.sandbox_keyword'))
                                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <p class="text-sm font-semibold text-amber-800 flex items-center gap-1">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                    Sandbox Setup Required
                                                </p>
                                                <p class="text-xs text-amber-700 mt-1">Before reporting via WhatsApp, each user must join the Twilio sandbox:</p>
                                                <ol class="text-xs text-amber-700 mt-2 ml-4 list-decimal space-y-1">
                                                    <li>Open WhatsApp and add <span class="font-mono font-bold">+1 (415) 523-8886</span></li>
                                                    <li>Send: <span class="font-mono font-bold bg-amber-100 px-1 rounded">join {{ config('services.twilio.sandbox_keyword') }}</span></li>
                                                    <li>Wait for confirmation, then scan the QR code above</li>
                                                </ol>
                                                <p class="text-xs text-amber-600 mt-2 italic">One-time setup per phone number (lasts 72 hours).</p>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <x-molecules.empty-state
                                icon="folder-plus"
                                title="No crises configured yet"
                                body="Use the form above to create the first crisis instance."
                                colspan="7"
                            />
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
