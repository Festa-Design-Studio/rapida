<?php

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Services\ConflictModeService;
use App\Services\ReportSubmissionService;

new class extends Component {
    use WithFileUploads;

    public Crisis $crisis;

    public int $currentStep = 1;

    public int $totalSteps = 6;

    public bool $conflictMode = false;

    /** Accumulated step data from child components */
    public array $stepData = [];

    /** Step 1: Photo (kept for file upload binding) */
    public $photo = null;

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

    public ?float $aiConfidence = null;

    /** Step 7: Confirmation */
    public ?string $reportId = null;

    public int $communityReportCount = 0;

    /** Error state */
    public ?string $submitError = null;

    public function mount(): void
    {
        $this->conflictMode = app(ConflictModeService::class)->isConflict($this->crisis);
    }

    public function nextStep(): void
    {
        $this->syncStepData();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        $this->syncStepData();

        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->syncStepData();
            $this->currentStep = $step;
        }
    }

    #[On('step-completed')]
    public function onStepCompleted(array $data): void
    {
        $this->stepData = array_merge($this->stepData, $data);
        $this->hydrateFromStepData($data);
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

        $this->syncStepData();

        if ($this->currentStep === 2) {
            $this->currentStep = 3;
        }
    }

    #[On('report-submitted')]
    public function onReportSubmitted(array $data): void
    {
        $this->reportId = $data['reportId'] ?? null;
        $this->communityReportCount = $data['communityReportCount'] ?? 0;
        $this->currentStep = 7;
    }

    public function submit(): void
    {
        $this->syncStepData();
        $this->submitError = null;

        try {
            $dto = new SubmitReportData(
                crisis: $this->crisis,
                latitude: $this->stepData['latitude'] ?? $this->latitude,
                longitude: $this->stepData['longitude'] ?? $this->longitude,
                landmarkText: $this->stepData['landmarkText'] ?? $this->landmarkText,
                damageLevel: $this->stepData['damageLevel'] ?? $this->damageLevel ?: null,
                infrastructureType: $this->resolveInfrastructureType(),
                crisisType: $this->stepData['crisisType'] ?? $this->crisisType ?: null,
                infrastructureName: $this->stepData['infrastructureName'] ?? $this->infrastructureName,
                debrisRequired: $this->resolveDebrisRequired(),
                description: $this->stepData['description'] ?? $this->description,
                deviceFingerprintId: $this->conflictMode ? null : request()->cookie('rapida_device_fingerprint'),
                accountId: auth()->id(),
                buildingFootprintId: $this->stepData['buildingFootprintId'] ?? $this->buildingFootprintId,
                locationMethod: $this->stepData['locationMethod'] ?? $this->locationMethod,
                submittedVia: 'web',
                photoGuidanceShown: true,
                moduleResponses: $this->stepData['moduleResponses'] ?? $this->moduleResponses,
                photoFile: $this->photo,
            );

            $report = app(ReportSubmissionService::class)->submit($dto);

            $this->reportId = $report->id;
            $this->communityReportCount = \App\Models\DamageReport::where('crisis_id', $this->crisis->id)->count();
            $this->currentStep = 7;
        } catch (\Throwable $e) {
            report($e);
            $this->submitError = __('wizard.submit_error');
        }
    }

    /** Sync current properties into stepData array. Photo lives on
     * $this->photo directly (bound by step-photo via $parent.photo) and
     * does not need to traverse stepData. */
    private function syncStepData(): void
    {
        $this->stepData = array_merge($this->stepData, array_filter([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'buildingFootprintId' => $this->buildingFootprintId,
            'locationMethod' => $this->locationMethod,
            'landmarkText' => $this->landmarkText,
            'damageLevel' => $this->damageLevel,
            'infrastructureTypes' => $this->infrastructureTypes,
            'crisisType' => $this->crisisType,
            'infrastructureName' => $this->infrastructureName,
            'debrisRequired' => $this->debrisRequired,
            'description' => $this->description,
            'moduleResponses' => $this->moduleResponses,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []));
    }

    /** Hydrate shell properties from incoming step data */
    private function hydrateFromStepData(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    private function resolveInfrastructureType(): ?string
    {
        $types = $this->stepData['infrastructureTypes'] ?? $this->infrastructureTypes;

        return is_array($types) ? ($types[0] ?? 'other') : 'other';
    }

    private function resolveDebrisRequired(): ?bool
    {
        $debris = $this->stepData['debrisRequired'] ?? $this->debrisRequired;

        return match ($debris) {
            'yes' => true,
            'no' => false,
            default => null,
        };
    }
};
?>

<div class="flex flex-col min-h-screen bg-surface-page" style="min-height: 100dvh;">
    {{-- Transparency screen gate (shown once per device per crisis) --}}
    <x-organisms.transparency-onboarding
        :crisisSlug="$crisis->slug"
        :crisisName="$crisis->name"
        :conflictContext="$conflictMode"
    />

    {{-- Conflict mode banner (persistent when conflict_context = true) --}}
    <x-molecules.conflict-mode-banner :show="$conflictMode" />

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
        @if($currentStep === 1)
            {{-- Photo upload is inlined here (not extracted to a child Livewire
                 component) because Livewire serialises TemporaryUploadedFile
                 to a string path when it crosses a component boundary. The
                 #[Modelable] pattern works for primitives but not for file
                 uploads, so the file input must live on the same component
                 (wizard-shell) that has WithFileUploads + $photo. --}}
            <div
                class="flex flex-col gap-6"
                x-data="{
                    state: 'idle',
                    error: null,
                    async handle(event) {
                        const file = event.target.files && event.target.files[0];
                        if (!file) return;
                        this.error = null;
                        this.state = 'compressing';
                        const result = await window.rapidaCompressPhoto(file);
                        if (!result.ok) {
                            this.state = 'idle';
                            this.error = result.reason === 'photo_too_large'
                                ? @js(__('rapida.photo_too_large'))
                                : @js(__('wizard.submit_error'));
                            return;
                        }
                        this.state = 'uploading';
                        $wire.upload('photo', result.file, () => { this.state = 'idle'; }, () => { this.state = 'idle'; });
                    },
                }"
            >
                <x-molecules.photo-guidance-drawer :crisisType="$crisis->crisis_type_default ?? 'earthquake'" />

                <div class="flex flex-col gap-2">
                    <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_1_title') }}</h1>
                    <p class="text-body text-slate-600">{{ __('wizard.step_1_desc') }}</p>
                </div>

                <div class="flex flex-col gap-1.5">
                    <span class="text-label font-medium text-slate-700">{{ __('wizard.step_1_label') }}</span>

                    @if($photo)
                        <div class="relative rounded-xl border-2 border-rapida-blue-700 overflow-hidden">
                            <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('wizard.step_1_label') }}" class="w-full max-h-64 object-cover" />
                            <div class="flex items-center justify-center gap-4 px-4 py-2 bg-slate-50 border-t border-slate-200">
                                <label for="photo-replace" class="text-body-sm font-medium text-rapida-blue-700 hover:text-rapida-blue-900 cursor-pointer">{{ __('wizard.step_1_change') }}</label>
                                <span class="text-slate-300">|</span>
                                <button type="button" wire:click="$set('photo', null)" class="text-body-sm font-medium text-crisis-rose-600 hover:text-crisis-rose-800">{{ __('wizard.step_1_remove') }}</button>
                            </div>
                            <input id="photo-replace" type="file" accept="image/*" capture="environment" @change="handle($event)" class="sr-only" />
                        </div>
                    @else
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
                            <input id="photo-input" type="file" accept="image/*" capture="environment" @change="handle($event)" class="sr-only" />
                        </label>
                    @endif

                    {{-- Gap-50: client-side compression indicator. Shows
                         "Optimizing..." while browser-image-compression runs in
                         a Web Worker, then "Uploading..." while $wire.upload
                         streams the smaller File to Livewire's temp storage. --}}
                    <div x-show="state === 'compressing'" class="flex items-center gap-2 text-body-sm text-rapida-blue-700" x-cloak>
                        <x-atoms.loader size="sm" />
                        <span>{{ __('wizard.step_1_compressing') }}</span>
                    </div>
                    <div x-show="state === 'uploading'" class="flex items-center gap-2 text-body-sm text-rapida-blue-700" x-cloak>
                        <x-atoms.loader size="sm" />
                        <span>{{ __('wizard.step_1_uploading') }}</span>
                    </div>

                    <p x-show="error" x-text="error" class="text-body-sm text-crisis-rose-600" role="alert" x-cloak></p>

                    @error('photo')
                        <p class="text-body-sm text-crisis-rose-600" role="alert">{{ $message }}</p>
                    @enderror

                    <p class="text-body-sm text-slate-500">{{ __('wizard.step_1_help') }}</p>
                </div>
            </div>
        @elseif($currentStep === 2)
            <livewire:wizard.step-location
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                wire:key="step-location"
            />
        @elseif($currentStep === 3)
            <livewire:wizard.step-damage
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                :aiSuggestedLevel="$aiSuggestedLevel"
                :aiConfidence="$aiConfidence"
                :hasPhoto="$photo !== null"
                wire:key="step-damage"
            />
        @elseif($currentStep === 4)
            <livewire:wizard.step-infrastructure
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                wire:key="step-infrastructure"
            />
        @elseif($currentStep === 5)
            <livewire:wizard.step-modular
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                wire:key="step-modular"
            />
        @elseif($currentStep === 6)
            <livewire:wizard.step-review
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                :stepData="$stepData"
                :photo="$photo"
                wire:key="step-review"
            />
        @elseif($currentStep === 7)
            <livewire:wizard.step-confirmation
                :crisis="$crisis"
                :reportId="$reportId"
                :communityReportCount="$communityReportCount"
                :damageLevel="$damageLevel"
                wire:key="step-confirmation"
            />
        @endif
    </div>

    {{-- Bottom navigation --}}
    @if($currentStep >= 1 && $currentStep <= $totalSteps)
        <div class="px-6 pb-6 pt-2 flex flex-col gap-3" style="padding-bottom: max(1.5rem, env(safe-area-inset-bottom, 1.5rem));">
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
                        wire:key="btn-next"
                        class="flex-1 min-h-[48px]"
                    >
                        {{ __('wizard.btn_continue') }}
                    </x-atoms.button>
                @elseif($currentStep === $totalSteps)
                    <x-atoms.button
                        variant="primary"
                        size="lg"
                        wire:click="submit"
                        wire:key="btn-submit"
                        class="flex-1 min-h-[48px] data-[loading]:cursor-wait data-[loading]:pointer-events-none data-[loading]:opacity-70"
                    >
                        <span class="data-[loading]:hidden">{{ __('wizard.btn_submit') }}</span>
                        <span class="hidden data-[loading]:inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            {{ __('wizard.btn_submitting') }}
                        </span>
                    </x-atoms.button>
                @endif
            </div>
        </div>
    @endif

    {{-- Client-side error handling for network/CSRF failures (Livewire 4 API) --}}
    @script
    <script>
        try {
            Livewire.interceptRequest(({ onError, onFailure }) => {
                onError(({ response, preventDefault }) => {
                    if (response.status === 419) {
                        preventDefault();
                        $wire.set('submitError', @js(__('wizard.session_expired_action')));
                    }
                });

                onFailure(({ error }) => {
                    window.dispatchEvent(new Event('livewire-network-error'));
                });
            });
        } catch (e) {
            console.error('[wizard] interceptRequest failed:', e);
        }
    </script>
    @endscript
</div>
