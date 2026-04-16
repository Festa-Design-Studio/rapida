<?php

use Livewire\Component;
use App\Models\Crisis;
use App\DataTransferObjects\SubmitReportData;
use App\Services\ReportSubmissionService;

new class extends Component {
    public Crisis $crisis;

    public bool $conflictMode = false;

    /** Collected data from previous steps */
    public array $stepData = [];

    /** Error state */
    public ?string $submitError = null;

    public function submit(): void
    {
        $this->submitError = null;

        try {
            $dto = new SubmitReportData(
                crisis: $this->crisis,
                latitude: $this->stepData['latitude'] ?? null,
                longitude: $this->stepData['longitude'] ?? null,
                landmarkText: $this->stepData['landmarkText'] ?? null,
                damageLevel: $this->stepData['damageLevel'] ?? null,
                infrastructureType: is_array($this->stepData['infrastructureTypes'] ?? null)
                    ? ($this->stepData['infrastructureTypes'][0] ?? 'other')
                    : 'other',
                crisisType: $this->stepData['crisisType'] ?? null,
                infrastructureName: $this->stepData['infrastructureName'] ?? null,
                debrisRequired: match ($this->stepData['debrisRequired'] ?? null) {
                    'yes' => true,
                    'no' => false,
                    default => null,
                },
                description: $this->stepData['description'] ?? null,
                deviceFingerprintId: $this->conflictMode ? null : request()->input('device_fingerprint_id'),
                accountId: auth()->id(),
                buildingFootprintId: $this->stepData['buildingFootprintId'] ?? null,
                locationMethod: $this->stepData['locationMethod'] ?? 'coordinate_only',
                submittedVia: 'web',
                photoGuidanceShown: true,
                moduleResponses: $this->stepData['moduleResponses'] ?? [],
                photoFile: $this->stepData['photo'] ?? null,
            );

            $report = app(ReportSubmissionService::class)->submit($dto);

            $communityCount = \App\Models\DamageReport::where('crisis_id', $this->crisis->id)->count();

            $this->dispatch('report-submitted', data: [
                'reportId' => $report->id,
                'communityReportCount' => $communityCount,
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->submitError = __('wizard.submit_error');
        }
    }
};
?>

<div class="flex flex-col gap-6">
    @if($submitError)
        <div class="rounded-lg bg-crisis-rose-50 border border-crisis-rose-200 p-4" role="alert">
            <p class="text-body-sm text-crisis-rose-900">{{ $submitError }}</p>
        </div>
    @endif

    {{-- Network error banner (Alpine-managed) --}}
    <div x-data="{ show: false }"
         x-on:livewire-network-error.window="show = true"
         x-show="show"
         x-cloak
         class="rounded-lg bg-alert-amber-50 border border-alert-amber-100 p-4 flex items-center gap-3"
         role="alert"
    >
        <p class="text-body-sm text-alert-amber-900 flex-1">{{ __('wizard.network_error') }}</p>
        <button type="button" @click="show = false"
                class="text-body-sm font-medium text-alert-amber-700 hover:text-alert-amber-900 shrink-0">
            {{ __('wizard.btn_dismiss') }}
        </button>
    </div>

    <div class="flex flex-col gap-2">
        <h1 class="text-h1 font-heading font-bold text-slate-900">{{ __('wizard.step_6_title') }}</h1>
        <p class="text-body text-slate-600">{{ __('wizard.step_6_desc') }}</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        {{-- Photo preview --}}
        <div class="h-48 bg-slate-100 flex items-center justify-center border-b border-slate-200 overflow-hidden">
            @if(isset($stepData['photo']) && $stepData['photo'])
                <img src="{{ $stepData['photo']->temporaryUrl() }}" alt="{{ __('wizard.step_1_label') }}" class="w-full h-full object-cover" />
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
                @if(! empty($stepData['damageLevel']))
                    <x-atoms.badge variant="{{ $stepData['damageLevel'] }}">{{ ucfirst($stepData['damageLevel']) }}</x-atoms.badge>
                @else
                    <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                @endif
            </div>

            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <span class="text-body-sm text-slate-600">{{ __('wizard.review_infra') }}</span>
                @if(! empty($stepData['infrastructureTypes']))
                    <div class="flex flex-wrap gap-1 justify-end">
                        @foreach($stepData['infrastructureTypes'] as $type)
                            <x-atoms.badge variant="info">{{ ucfirst(str_replace('_', ' ', $type)) }}</x-atoms.badge>
                        @endforeach
                    </div>
                @else
                    <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                @endif
            </div>

            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <span class="text-body-sm text-slate-600">{{ __('wizard.review_crisis') }}</span>
                @if(! empty($stepData['crisisType']))
                    <span class="text-body-sm font-medium">{{ ucfirst($stepData['crisisType']) }}</span>
                @else
                    <span class="text-body-sm text-slate-400">{{ __('wizard.not_selected') }}</span>
                @endif
            </div>

            <div class="flex items-center justify-between py-2">
                <span class="text-body-sm text-slate-600">{{ __('wizard.review_location') }}</span>
                @if(! empty($stepData['latitude']) && ! empty($stepData['longitude']))
                    <span class="text-body-sm font-medium">{{ number_format($stepData['latitude'], 4) }}, {{ number_format($stepData['longitude'], 4) }}</span>
                @elseif(! empty($stepData['landmarkText']))
                    <span class="text-body-sm font-medium">{{ $stepData['landmarkText'] }}</span>
                @else
                    <span class="text-body-sm text-slate-400">{{ __('wizard.not_provided') }}</span>
                @endif
            </div>

            @if(! empty($stepData['description']))
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-body-sm text-slate-600 mb-1">{{ __('wizard.review_description') }}</p>
                    <p class="text-body-sm text-slate-800">{{ $stepData['description'] }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="rounded-lg bg-ground-green-50 border border-ground-green-200 p-4 text-center">
        <p class="text-body-sm text-ground-green-900">{{ __('wizard.partial_submit_note') }}</p>
    </div>
</div>
