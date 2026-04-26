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
                deviceFingerprintId: $this->conflictMode ? null : request()->cookie(\App\Http\Middleware\EnsureDeviceFingerprint::COOKIE),
                accountId: auth()->id(),
                buildingFootprintId: $this->stepData['buildingFootprintId'] ?? $this->buildingFootprintId,
                locationMethod: $this->stepData['locationMethod'] ?? $this->locationMethod,
                submittedVia: 'web',
                photoGuidanceShown: true,
                moduleResponses: $this->stepData['moduleResponses'] ?? $this->moduleResponses,
                photoFile: $this->stepData['photo'] ?? $this->photo,
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

    /** Sync current properties into stepData array */
    private function syncStepData(): void
    {
        $this->stepData = array_merge($this->stepData, array_filter([
            'photo' => $this->photo,
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
            <livewire:wizard.step-photo
                :crisis="$crisis"
                :conflictMode="$conflictMode"
                wire:key="step-photo"
            />
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
