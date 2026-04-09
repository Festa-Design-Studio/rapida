<?php

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Crisis;

new class extends Component {
    use WithFileUploads;

    public Crisis $crisis;

    public int $currentStep = 1;

    public int $totalSteps = 6;

    /** Step 1: Photo */
    public $photo = null; // Livewire TemporaryUploadedFile

    /** Step 2: Location */
    public ?string $buildingFootprintId = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $locationMethod = 'coordinate_only';

    public ?string $landmarkText = null;

    /** Step 3: Damage */
    public string $damageLevel = '';

    /** Step 4: Infrastructure */
    public array $infrastructureTypes = [];

    public string $crisisType = '';

    public ?string $infrastructureName = null;

    public ?string $debrisRequired = null;

    public ?string $description = null;

    /** Step 5: Modular questions */
    public array $moduleResponses = [
        'electricity_condition' => '',
        'health_functioning' => '',
        'pressing_needs_needs' => [],
    ];

    /** Step 3: AI suggestion */
    public ?string $aiSuggestedLevel = null;

    /** Step 7: Confirmation */
    public ?string $reportId = null;

    public int $communityReportCount = 0;

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    public function selectLandmark(string $id, float $lat, float $lng, string $name): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->landmarkText = $name;
        $this->locationMethod = 'landmark_picker';
    }

    #[On('building-selected')]
    public function onBuildingSelected(
        string $id,
        float $latitude,
        float $longitude,
        ?string $damage_level = null
    ): void {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        if (str_starts_with($id, 'point-')) {
            $this->buildingFootprintId = null;
            $this->locationMethod = 'coordinate_only';
        } else {
            $this->buildingFootprintId = $id;
            $this->locationMethod = 'footprint_tap';
        }

        if ($damage_level) {
            $this->damageLevel = $damage_level;
        }
        if ($this->currentStep === 2) {
            $this->currentStep = 3;
        }
    }

    public function submit(): void
    {
        $isConflict = $this->crisis->conflict_context ?? false;

        $report = \App\Models\DamageReport::create([
            'crisis_id' => $this->crisis->id,
            'building_footprint_id' => $this->buildingFootprintId,
            'account_id' => auth()->id(),
            'device_fingerprint_id' => $isConflict ? null : request()->input('device_fingerprint_id'),
            'photo_url' => $this->photo ? $this->photo->store('photos', 'public') : 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            'photo_hash' => $this->photo ? hash_file('sha256', $this->photo->getRealPath()) : hash('sha256', 'placeholder'),
            'photo_guidance_shown' => true,
            'damage_level' => $this->damageLevel ?: 'partial',
            'ai_suggested_level' => $this->aiSuggestedLevel,
            'infrastructure_type' => is_array($this->infrastructureTypes) ? ($this->infrastructureTypes[0] ?? 'other') : 'other',
            'crisis_type' => $this->crisisType ?: 'flood',
            'infrastructure_name' => $this->infrastructureName,
            'debris_required' => $this->debrisRequired === 'yes',
            'location_method' => $this->locationMethod,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'landmark_text' => $this->landmarkText,
            'description' => $this->description,
            'completeness_score' => $this->calculateCompletenessScore(),
            'submitted_via' => 'web',
            'reporter_tier' => $isConflict ? 'anonymous' : 'anonymous',
            'submitted_at' => now(),
            'synced_at' => now(),
            'is_flagged' => false,
        ]);

        // Save modular question responses
        $moduleKeyMap = [
            'electricity_condition' => ['electricity', 'condition'],
            'health_functioning' => ['health', 'functioning'],
            'pressing_needs_needs' => ['pressing_needs', 'needs'],
        ];
        foreach ($this->moduleResponses as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            if (! isset($moduleKeyMap[$key])) {
                continue;
            }
            [$moduleKey, $fieldKey] = $moduleKeyMap[$key];
            \App\Models\ReportModule::create([
                'report_id' => $report->id,
                'module_key' => $moduleKey,
                'field_key' => $fieldKey,
                'value' => is_array($value) ? $value : [$value],
            ]);
        }

        $this->reportId = $report->id;
        $this->communityReportCount = \App\Models\DamageReport::where('crisis_id', $this->crisis->id)->count();
        $this->currentStep = 7;

        \App\Events\ReportSubmitted::dispatch($report);
    }

    public function calculateCompletenessScore(): int
    {
        return app(\App\Services\CompletenessScoreService::class)->scoreFromArray([
            'photo_url' => $this->photo ? 'uploaded' : null,
            'latitude' => $this->latitude,
            'landmark_text' => $this->landmarkText,
            'damage_level' => $this->damageLevel,
            'infrastructure_type' => is_array($this->infrastructureTypes) ? ($this->infrastructureTypes[0] ?? null) : null,
            'crisis_type' => $this->crisisType,
            'infrastructure_name' => $this->infrastructureName,
        ]);
    }
};
?>

<div class="flex flex-col min-h-screen bg-surface-page">
    {{-- Transparency screen gate (shown once per device per crisis) --}}
    <x-organisms.transparency-onboarding
        :crisisSlug="$crisis->slug"
        :crisisName="$crisis->name"
        :conflictContext="$crisis->conflict_context ?? false"
    />

    {{-- Conflict mode banner (persistent when conflict_context = true) --}}
    <x-molecules.conflict-mode-banner :show="$crisis->conflict_context ?? false" />

    {{-- Progress indicator --}}
    @if($currentStep <= $totalSteps)
        <div class="px-6 pt-6 pb-2">
            <x-atoms.progress-step :current="$currentStep" :total="$totalSteps" variant="dots" />
            <p class="text-body-sm text-slate-500 text-center mt-1">
                {{ __('wizard.step_of', ['current' => $currentStep, 'total' => $totalSteps]) }}
            </p>
        </div>
    @endif

    {{-- Step content --}}
    <div class="flex-1 px-6 py-4">

        {{-- ========== STEP 1: PHOTO ========== --}}
        @if($currentStep === 1)
            {{-- Photo guidance drawer (pre-screen, once per session) --}}
            <x-molecules.photo-guidance-drawer :crisisType="$crisis->crisis_type_default ?? 'earthquake'" />

            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_1_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_1_desc') }}</p>
                </div>

                <div class="flex flex-col gap-1.5">
                    <span class="text-label font-medium text-slate-700">{{ __('wizard.step_1_label') }}</span>

                    @if($photo)
                        {{-- Preview --}}
                        <div class="relative rounded-xl border-2 border-rapida-blue-700 overflow-hidden">
                            <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('wizard.step_1_label') }}" class="w-full max-h-64 object-cover" />
                            <div class="flex items-center justify-center gap-4 px-4 py-2 bg-slate-50 border-t border-slate-200">
                                <label for="photo-replace" class="text-body-sm font-medium text-rapida-blue-700 hover:text-rapida-blue-900 cursor-pointer">{{ __('wizard.step_1_change') }}</label>
                                <span class="text-slate-300">|</span>
                                <button type="button" wire:click="$set('photo', null)" class="text-body-sm font-medium text-red-600 hover:text-red-800">{{ __('wizard.step_1_remove') }}</button>
                            </div>
                            <input id="photo-replace" type="file" accept="image/*" capture="environment" wire:model="photo" class="sr-only" />
                        </div>
                    @else
                        {{-- Empty upload zone --}}
                        <label
                            for="photo-input"
                            class="relative flex flex-col items-center justify-center min-h-[160px] rounded-xl border-2 border-dashed border-slate-300 bg-slate-50
                                   hover:border-rapida-blue-500 hover:bg-rapida-blue-50 cursor-pointer transition-colors duration-150"
                        >
                            <div class="flex flex-col items-center gap-3 p-6 text-center">
                                <x-atoms.icon name="camera" size="xl" class="text-slate-400" />
                                <div>
                                    <p class="text-body-sm font-medium text-slate-700">{{ __('wizard.step_1_upload_prompt') }}</p>
                                    <p class="text-caption text-slate-400 mt-1">{{ __('wizard.step_1_upload_formats') }}</p>
                                </div>
                            </div>
                            <input id="photo-input" type="file" accept="image/*" capture="environment" wire:model="photo" class="sr-only" />
                        </label>
                    @endif

                    {{-- Loading state --}}
                    <div wire:loading wire:target="photo" class="flex items-center gap-2 text-body-sm text-rapida-blue-700">
                        <x-atoms.loader size="sm" />
                        <span>{{ __('wizard.step_1_uploading') }}</span>
                    </div>

                    @error('photo')
                        <p class="text-body-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror

                    <p class="text-body-sm text-slate-500">{{ __('wizard.step_1_help') }}</p>
                </div>
            </div>

        {{-- ========== STEP 2: LOCATION ========== --}}
        @elseif($currentStep === 2)
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_2_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_2_desc') }}</p>
                </div>

                @if($latitude && $longitude)
                    <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-4 flex items-center gap-3">
                        <x-atoms.icon name="check-circle" size="md" class="text-ground-green-700" />
                        <div>
                            <p class="text-body-sm font-medium text-ground-green-900">{{ __('wizard.step_2_location_selected') }}</p>
                            <p class="text-caption text-ground-green-700">{{ number_format($latitude, 5) }}, {{ number_format($longitude, 5) }}</p>
                        </div>
                    </div>
                @endif

                <div
                    x-data="typeof rapidaMap !== 'undefined' ? rapidaMap({
                        crisisSlug: '{{ $crisis->slug }}',
                        mode: 'reporter',
                        center: [-0.20, 5.56],
                        zoom: 14,
                        tokens: {
                            damage_minimal: '#22c55e',   // damage-minimal-map
                            damage_partial: '#f59e0b',   // damage-partial-map
                            damage_complete: '#c46b5a',  // crisis-rose-400
                            footprint_fill: '#2e6689',   // rapida-blue-700
                            footprint_stroke: '#1a3a4a', // rapida-blue-900
                            user_dot: '#2e6689',         // rapida-blue-700
                        },
                        buildingsUrl: '/api/v1/crises/{{ $crisis->slug }}/buildings',
                        pinsUrl: '/api/v1/crises/{{ $crisis->slug }}/pins',
                    }) : {}"
                    x-init="if (typeof rapidaMap !== 'undefined' && init) init()"
                    wire:ignore
                    id="rapida-map-wizard"
                    class="w-full h-[300px] rounded-lg overflow-hidden bg-slate-100"
                    role="application"
                    aria-label="{{ __('wizard.map_aria_label') }}"
                ></div>

                <p class="text-body-sm text-slate-500">{{ __('wizard.step_2_or_describe') }}</p>

                <x-atoms.text-input
                    name="landmark_text"
                    :label="__('wizard.step_2_landmark_label')"
                    :placeholder="__('wizard.step_2_landmark_placeholder')"
                    :help="__('wizard.step_2_landmark_help')"
                    wire:model.live.debounce.500ms="landmarkText"
                />

                {{-- Landmark picker --}}
                @php
                    $landmarks = \App\Models\Landmark::where('crisis_id', $crisis->id)->get();
                @endphp
                @if($landmarks->isNotEmpty())
                    <div class="mt-4">
                        <p class="text-label font-medium text-slate-700 mb-2">{{ __('wizard.landmark_picker_label', [], app()->getLocale()) }}</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($landmarks as $lm)
                                <button
                                    type="button"
                                    wire:click="selectLandmark('{{ $lm->id }}', {{ $lm->latitude }}, {{ $lm->longitude }}, '{{ addslashes($lm->name) }}')"
                                    class="text-left p-3 rounded-lg border transition-colors duration-150
                                           {{ $landmarkText === $lm->name ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500 hover:bg-rapida-blue-50/50' }}"
                                >
                                    <p class="text-body-sm font-medium text-slate-900 truncate">{{ $lm->name }}</p>
                                    <p class="text-caption text-slate-500">{{ ucfirst($lm->type ?? 'Landmark') }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        {{-- ========== STEP 3: DAMAGE LEVEL ========== --}}
        @elseif($currentStep === 3)
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_3_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_3_desc') }}</p>
                </div>

                {{-- AI turn-taking (Gap V6b) --}}
                @if($aiSuggestedLevel)
                    <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 p-4 flex items-start gap-3">
                        <x-atoms.icon name="info" size="md" class="text-rapida-blue-700 shrink-0 mt-0.5" />
                        <div>
                            <p class="text-body-sm font-medium text-rapida-blue-900">
                                {{ __('rapida.ai_suggestion_prompt', ['level' => ucfirst($aiSuggestedLevel)]) }}
                            </p>
                        </div>
                    </div>
                @elseif(! $damageLevel && $photo)
                    {{-- AI is still processing — show analysing state --}}
                    <div class="flex items-center gap-2 text-body-sm text-rapida-blue-700">
                        <x-atoms.loader size="sm" />
                        <span>{{ __('rapida.ai_analyzing') }}</span>
                    </div>
                @endif

                <fieldset class="flex flex-col gap-3" wire:model.live="damageLevel">
                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                                {{ $damageLevel === 'minimal' ? 'border-damage-minimal-ui bg-damage-minimal-ui-surface' : 'border-slate-200 hover:border-ground-green-500' }}">
                        <input type="radio" name="damage_level" value="minimal" wire:model.live="damageLevel" class="sr-only" />
                        <div class="w-4 h-4 rounded-full bg-damage-minimal-map shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_minimal') }}</p>
                            <p class="text-body-sm text-slate-500">{{ __('wizard.damage_minimal_desc') }}</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                                {{ $damageLevel === 'partial' ? 'border-damage-partial-ui bg-damage-partial-ui-surface' : 'border-slate-200 hover:border-alert-amber-500' }}">
                        <input type="radio" name="damage_level" value="partial" wire:model.live="damageLevel" class="sr-only" />
                        <div class="w-4 h-4 rounded-full bg-damage-partial-map shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_partial') }}</p>
                            <p class="text-body-sm text-slate-500">{{ __('wizard.damage_partial_desc') }}</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150
                                {{ $damageLevel === 'complete' ? 'border-damage-complete-ui bg-damage-complete-ui-surface' : 'border-slate-200 hover:border-crisis-rose-400' }}">
                        <input type="radio" name="damage_level" value="complete" wire:model.live="damageLevel" class="sr-only" />
                        <div class="w-4 h-4 rounded-full bg-damage-complete-map shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-body font-medium text-slate-900">{{ __('wizard.damage_complete') }}</p>
                            <p class="text-body-sm text-slate-500">{{ __('wizard.damage_complete_desc') }}</p>
                        </div>
                    </label>
                </fieldset>
            </div>

        {{-- ========== STEP 4: INFRASTRUCTURE DETAILS ========== --}}
        @elseif($currentStep === 4)
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_4_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_4_desc') }}</p>
                </div>

                {{-- Infrastructure type --}}
                <fieldset class="flex flex-col gap-2">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.infra_type_label') }} <span class="text-red-600">*</span></legend>
                    <p class="text-body-sm text-slate-500 mb-2">{{ __('wizard.infra_select_all') }}</p>
                    @foreach([
                        'commercial' => ['infra_commercial', 'infra_commercial_desc'],
                        'government' => ['infra_government', 'infra_government_desc'],
                        'utility' => ['infra_utility', 'infra_utility_desc'],
                        'transport' => ['infra_transport', 'infra_transport_desc'],
                        'community' => ['infra_community', 'infra_community_desc'],
                        'public_recreation' => ['infra_public_recreation', 'infra_public_recreation_desc'],
                        'other' => ['infra_other', 'infra_other_desc'],
                    ] as $value => [$labelKey, $descKey])
                        <label class="flex items-start gap-3 px-4 py-3 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ in_array($value, $infrastructureTypes) ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="checkbox" value="{{ $value }}" wire:model.live="infrastructureTypes"
                                   class="mt-0.5 h-5 w-5 rounded accent-rapida-blue-700" />
                            <div>
                                <p class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</p>
                                <p class="text-body-sm text-slate-500">{{ __("wizard.{$descKey}") }}</p>
                            </div>
                        </label>
                    @endforeach
                </fieldset>

                {{-- Crisis type --}}
                <fieldset class="flex flex-col gap-2">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.crisis_type_label') }} <span class="text-red-600">*</span></legend>
                    @foreach([
                        'flood' => 'crisis_flood',
                        'earthquake' => 'crisis_earthquake',
                        'hurricane' => 'crisis_hurricane',
                        'wildfire' => 'crisis_wildfire',
                        'explosion' => 'crisis_explosion',
                        'conflict' => 'crisis_conflict',
                    ] as $value => $labelKey)
                        <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ $crisisType === $value ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="radio" name="crisis_type" value="{{ $value }}" wire:model.live="crisisType"
                                   class="h-5 w-5 accent-rapida-blue-700" />
                            <span class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</span>
                        </label>
                    @endforeach
                </fieldset>

                {{-- Debris --}}
                <fieldset class="flex flex-col gap-2">
                    <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.debris_label') }}</legend>
                    @foreach(['yes' => 'debris_yes', 'no' => 'debris_no', 'unknown' => 'debris_unknown'] as $value => $labelKey)
                        <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                    {{ $debrisRequired === $value ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                            <input type="radio" name="debris_required" value="{{ $value }}" wire:model.live="debrisRequired"
                                   class="h-5 w-5 accent-rapida-blue-700" />
                            <span class="text-body text-slate-900">{{ __("wizard.{$labelKey}") }}</span>
                        </label>
                    @endforeach
                </fieldset>

                {{-- Name + description --}}
                <x-atoms.text-input
                    name="infrastructure_name"
                    :label="__('wizard.infra_name_label')"
                    :placeholder="__('wizard.infra_name_placeholder')"
                    :help="__('wizard.infra_name_help')"
                    wire:model.live.debounce.500ms="infrastructureName"
                />

                <x-atoms.textarea
                    name="description"
                    :label="__('wizard.description_label')"
                    :placeholder="__('wizard.description_placeholder')"
                    :help="__('wizard.description_help')"
                    :maxlength="500"
                    wire:model.live.debounce.500ms="description"
                />
            </div>

        {{-- ========== STEP 5: MODULAR QUESTIONS ========== --}}
        @elseif($currentStep === 5)
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_5_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_5_desc') }}</p>
                </div>

                @php
                    $activeModules = $crisis->active_modules ?? ['electricity', 'health', 'pressing_needs'];
                @endphp

                @foreach($activeModules as $moduleKey)
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        @if($moduleKey === 'electricity')
                            <fieldset class="flex flex-col gap-3">
                                <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_electricity') }}</legend>
                                <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_electricity_q') }}</p>
                                @foreach([
                                    'no_damage' => 'module_option_no_damage',
                                    'minor' => 'module_option_minor',
                                    'moderate' => 'module_option_moderate',
                                    'severe' => 'module_option_severe',
                                    'destroyed' => 'module_option_destroyed',
                                    'unknown' => 'module_option_unknown',
                                ] as $optValue => $optKey)
                                    <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                                {{ ($moduleResponses['electricity_condition'] ?? '') === $optValue ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                                        <input type="radio" name="electricity_condition" value="{{ $optValue }}"
                                               wire:model.live="moduleResponses.electricity_condition"
                                               class="h-5 w-5 accent-rapida-blue-700" />
                                        <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                                    </label>
                                @endforeach
                            </fieldset>

                        @elseif($moduleKey === 'health')
                            <fieldset class="flex flex-col gap-3">
                                <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_health') }}</legend>
                                <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_health_q') }}</p>
                                @foreach([
                                    'fully_functional' => 'module_option_fully_functional',
                                    'partially_functional' => 'module_option_partially_functional',
                                    'largely_disrupted' => 'module_option_largely_disrupted',
                                    'not_functioning' => 'module_option_not_functioning',
                                    'unknown' => 'module_option_unknown',
                                ] as $optValue => $optKey)
                                    <label class="flex items-center gap-3 h-12 px-4 rounded-lg border cursor-pointer transition-colors duration-150
                                                {{ ($moduleResponses['health_functioning'] ?? '') === $optValue ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                                        <input type="radio" name="health_functioning" value="{{ $optValue }}"
                                               wire:model.live="moduleResponses.health_functioning"
                                               class="h-5 w-5 accent-rapida-blue-700" />
                                        <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                                    </label>
                                @endforeach
                            </fieldset>

                        @elseif($moduleKey === 'pressing_needs')
                            <fieldset class="flex flex-col gap-3">
                                <legend class="text-label font-medium text-slate-700 mb-1">{{ __('wizard.module_pressing_needs') }}</legend>
                                <p class="text-body-sm text-slate-600 mb-2">{{ __('wizard.module_pressing_needs_q') }}</p>
                                @foreach([
                                    'food_water' => 'needs_food_water',
                                    'cash' => 'needs_cash',
                                    'healthcare' => 'needs_healthcare',
                                    'shelter' => 'needs_shelter',
                                    'livelihoods' => 'needs_livelihoods',
                                    'wash' => 'needs_wash',
                                    'infrastructure' => 'needs_infrastructure',
                                    'protection' => 'needs_protection',
                                    'authority' => 'needs_authority',
                                    'other' => 'needs_other',
                                ] as $optValue => $optKey)
                                    <label class="flex items-start gap-3 px-4 py-3 rounded-lg border cursor-pointer transition-colors duration-150
                                                {{ in_array($optValue, $moduleResponses['pressing_needs_needs'] ?? []) ? 'border-rapida-blue-700 bg-rapida-blue-50' : 'border-slate-200 hover:border-rapida-blue-500' }}">
                                        <input type="checkbox" value="{{ $optValue }}"
                                               wire:model.live="moduleResponses.pressing_needs_needs"
                                               class="mt-0.5 h-5 w-5 rounded accent-rapida-blue-700" />
                                        <span class="text-body text-slate-900">{{ __("wizard.{$optKey}") }}</span>
                                    </label>
                                @endforeach
                            </fieldset>
                        @endif
                    </div>
                @endforeach
            </div>

        {{-- ========== STEP 6: REVIEW ========== --}}
        @elseif($currentStep === 6)
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_6_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_6_desc') }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
                    {{-- Photo preview --}}
                    <div class="h-48 bg-slate-100 flex items-center justify-center border-b border-slate-200 overflow-hidden">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('wizard.step_1_label') }}" class="w-full h-full object-cover" />
                        @else
                            <div class="text-center">
                                <x-atoms.icon name="camera" size="lg" class="text-slate-400 mx-auto" />
                                <p class="text-body-sm text-slate-400 mt-1">{{ __('wizard.no_photo') }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Summary --}}
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-body-sm text-slate-600">{{ __('wizard.review_damage') }}</span>
                            @if($damageLevel)
                                <x-atoms.badge variant="{{ $damageLevel }}">{{ ucfirst($damageLevel) }}</x-atoms.badge>
                            @else
                                <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-body-sm text-slate-600">{{ __('wizard.review_infra') }}</span>
                            @if(!empty($infrastructureTypes))
                                <div class="flex flex-wrap gap-1 justify-end">
                                    @foreach($infrastructureTypes as $type)
                                        <x-atoms.badge variant="info">{{ ucfirst(str_replace('_', ' ', $type)) }}</x-atoms.badge>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-slate-100">
                            <span class="text-body-sm text-slate-600">{{ __('wizard.review_crisis') }}</span>
                            @if($crisisType)
                                <span class="text-body-sm font-medium">{{ ucfirst($crisisType) }}</span>
                            @else
                                <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-body-sm text-slate-600">{{ __('wizard.review_location') }}</span>
                            @if($latitude && $longitude)
                                <span class="text-body-sm font-medium">{{ number_format($latitude, 4) }}, {{ number_format($longitude, 4) }}</span>
                            @elseif($landmarkText)
                                <span class="text-body-sm font-medium">{{ $landmarkText }}</span>
                            @else
                                <span class="text-body-sm text-slate-400">{{ __('wizard.not_provided') }}</span>
                            @endif
                        </div>

                        @if($description)
                            <div class="pt-2 border-t border-slate-100">
                                <p class="text-body-sm text-slate-600 mb-1">{{ __('wizard.review_description') }}</p>
                                <p class="text-body-sm text-slate-800">{{ $description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-4 text-center">
                    <p class="text-body-sm text-ground-green-900">{{ __('wizard.partial_submit_note') }}</p>
                </div>
            </div>

        {{-- ========== STEP 7: CONFIRMATION ========== --}}
        @elseif($currentStep === 7)
            <x-molecules.submission-confirmation
                :reportId="$reportId"
                :submittedAt="now()->toIso8601String()"
                :damageLevel="$damageLevel"
                syncStatus="synced"
            />

            {{-- Fix 3: Real-time impact counter --}}
            <p class="text-body text-slate-600 text-center mt-4">
                {{ __('wizard.impact_counter', ['count' => $communityReportCount]) }}
            </p>

            <x-organisms.engagement-panel
                :communityCount="$communityReportCount"
                :userReportCount="1"
            />

            <div class="flex flex-col gap-3 mt-6 w-full max-w-sm mx-auto">
                <a href="{{ route('submit') }}">
                    <x-atoms.button variant="primary" class="w-full">{{ __('wizard.btn_submit_another') }}</x-atoms.button>
                </a>
                <a href="{{ route('my-reports') }}">
                    <x-atoms.button variant="secondary" class="w-full">{{ __('wizard.btn_view_reports') }}</x-atoms.button>
                </a>
                <a href="{{ route('map-home') }}">
                    <x-atoms.button variant="ghost" class="w-full">{{ __('wizard.btn_back_to_map') }}</x-atoms.button>
                </a>
            </div>

            {{-- Fix 5: Post-submission account creation prompt --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 text-center mt-6">
                <h3 class="text-h4 font-heading font-medium text-slate-900">{{ __('wizard.account_offer_title') }}</h3>
                <p class="text-body-sm text-slate-500 mt-2">{{ __('wizard.account_offer_desc') }}</p>
                <div class="mt-4">
                    <a href="{{ route('account.register', ['report' => $reportId]) }}">
                        <x-atoms.button variant="secondary" size="md" class="w-full" type="button">
                            {{ __('wizard.account_create') }}
                        </x-atoms.button>
                    </a>
                </div>
                <p class="text-caption text-slate-400 mt-3">{{ __('wizard.account_skip') }}</p>
            </div>
        @endif
    </div>

    {{-- Bottom navigation --}}
    @if($currentStep >= 1 && $currentStep <= $totalSteps)
        <div class="px-6 pb-6 pt-2 flex flex-col gap-3">
            <p class="text-body-sm text-slate-500 text-center" aria-live="polite">
                {{ __('wizard.submit_anytime') }}
            </p>

            <div class="flex items-center gap-3">
                @if($currentStep > 1)
                    <x-atoms.button
                        variant="ghost"
                        wire:click="prevStep"
                        class="min-w-[48px]"
                    >
                        {{ __('wizard.btn_back') }}
                    </x-atoms.button>
                @endif

                @if($currentStep < $totalSteps)
                    <x-atoms.button
                        variant="primary"
                        wire:click="nextStep"
                        class="flex-1 min-h-[48px]"
                    >
                        {{ __('wizard.btn_continue') }}
                    </x-atoms.button>
                @elseif($currentStep === $totalSteps)
                    <x-atoms.button
                        variant="primary"
                        size="lg"
                        wire:click="submit"
                        class="flex-1 min-h-[48px]"
                    >
                        {{ __('wizard.btn_submit') }}
                    </x-atoms.button>
                @endif
            </div>
        </div>
    @endif
</div>
