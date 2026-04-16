<?php

use Livewire\Component;
use App\Models\Crisis;

new class extends Component {
    public Crisis $crisis;

    public ?string $reportId = null;

    public int $communityReportCount = 0;

    public string $damageLevel = '';
};
?>

<div class="flex flex-col gap-6">
    <x-molecules.submission-confirmation
        :reportId="$reportId"
        :submittedAt="now()->toIso8601String()"
        :damageLevel="$damageLevel"
        :syncStatus="$reportId ? 'synced' : 'pending'"
    />

    {{-- Real-time impact counter --}}
    <p class="text-body text-slate-600 text-center mt-4">
        {{ __('wizard.impact_counter', ['count' => $communityReportCount]) }}
    </p>

    <x-organisms.engagement-panel
        :communityCount="$communityReportCount"
        :userReportCount="1"
        :crisisId="$crisis->id"
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

    {{-- Post-submission account creation prompt --}}
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
</div>
