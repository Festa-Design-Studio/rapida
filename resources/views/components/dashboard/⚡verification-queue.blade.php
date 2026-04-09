<?php

use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    public function getVerificationReportsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return DamageReport::query()
            ->whereHas('verification', fn ($q) => $q->whereIn('status', ['pending', 'in_field']))
            ->with('verification')
            ->orderByDesc('submitted_at')
            ->get();
    }

    public function getRedundancyReportsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return DamageReport::query()
            ->where('is_flagged', true)
            ->whereDoesntHave('verification', fn ($q) => $q->whereIn('status', ['pending', 'in_field']))
            ->orderByDesc('submitted_at')
            ->get();
    }

    public function assign(string $reportId): void
    {
        \App\Models\Verification::updateOrCreate(
            ['report_id' => $reportId],
            [
                'assigned_to' => auth('undp')->id(),
                'status' => 'in_field',
                'assigned_at' => now(),
            ]
        );
    }

    public function verify(string $reportId): void
    {
        $report = DamageReport::find($reportId);
        $report?->verification?->update(['status' => 'verified', 'verified_at' => now()]);
        $report?->update(['is_flagged' => false]);
    }

    public function dispute(string $reportId): void
    {
        $report = DamageReport::find($reportId);
        $report?->verification?->update(['status' => 'disputed']);
    }

    public function dismissFlag(string $reportId): void
    {
        DamageReport::where('id', $reportId)->update(['is_flagged' => false]);
    }

    public function keepFlag(string $reportId): void
    {
        $this->assign($reportId);
    }
};
?>

<div
    x-data="{ activeTab: 'verification' }"
    class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden"
>
    {{-- Tab header --}}
    <div class="flex border-b border-slate-200">
        <button
            @click="activeTab = 'verification'"
            :class="activeTab === 'verification'
                ? 'border-rapida-blue-700 text-rapida-blue-900'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
            class="px-5 py-3 text-body-sm font-medium border-b-2 transition-colors flex items-center gap-2"
        >
            {{ __('rapida.tab_verification') }}
            <span class="inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 rounded-full text-[10px] font-semibold bg-rapida-blue-100 text-rapida-blue-900">
                {{ $this->verificationReports->count() }}
            </span>
        </button>
        <button
            @click="activeTab = 'redundancy'"
            :class="activeTab === 'redundancy'
                ? 'border-rapida-blue-700 text-rapida-blue-900'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
            class="px-5 py-3 text-body-sm font-medium border-b-2 transition-colors flex items-center gap-2"
        >
            {{ __('rapida.tab_redundancy') }}
            <span class="inline-flex items-center justify-center h-5 min-w-[20px] px-1.5 rounded-full text-[10px] font-semibold bg-alert-amber-50 text-alert-amber-900">
                {{ $this->redundancyReports->count() }}
            </span>
        </button>
    </div>

    {{-- Verification tab --}}
    <div x-show="activeTab === 'verification'" class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead class="bg-surface-page text-slate-600">
                <tr>
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.damage_level_label') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.ai_confidence_label') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.infrastructure') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.submitted') }}</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($this->verificationReports as $report)
                    <tr class="hover:bg-surface-page/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-caption text-slate-600">{{ Str::limit($report->id, 8, '...') }}</td>
                        <td class="px-4 py-3">
                            <x-atoms.badge :variant="$report->damage_level?->value ?? 'default'">
                                {{ $report->damage_level?->value ?? '—' }}
                            </x-atoms.badge>
                        </td>
                        <td class="px-4 py-3">
                            @if($report->ai_confidence !== null)
                                @php
                                    $pct = round($report->ai_confidence * 100);
                                    $tier = $report->ai_confidence > 0.85 ? 'high' : ($report->ai_confidence >= 0.60 ? 'medium' : 'low');
                                @endphp
                                <x-atoms.badge :variant="'confidence-' . $tier">{{ $pct }}%</x-atoms.badge>
                            @else
                                <span class="text-slate-400 text-caption">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $report->infrastructure_type) }}</td>
                        <td class="px-4 py-3 text-slate-600 text-caption">{{ $report->submitted_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <x-atoms.button size="sm" variant="primary" wire:click="verify('{{ $report->id }}')">
                                    Verify
                                </x-atoms.button>
                                <x-atoms.button size="sm" variant="secondary" wire:click="dispute('{{ $report->id }}')">
                                    Dispute
                                </x-atoms.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">{{ __('rapida.no_reports_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Redundancy tab --}}
    <div x-show="activeTab === 'redundancy'" x-cloak class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead class="bg-surface-page text-slate-600">
                <tr>
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.damage_level_label') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.infrastructure') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.submitted') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.location') }}</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($this->redundancyReports as $report)
                    <tr class="hover:bg-surface-page/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-caption text-slate-600">{{ Str::limit($report->id, 8, '...') }}</td>
                        <td class="px-4 py-3">
                            <x-atoms.badge :variant="$report->damage_level?->value ?? 'default'">
                                {{ $report->damage_level?->value ?? '—' }}
                            </x-atoms.badge>
                        </td>
                        <td class="px-4 py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $report->infrastructure_type) }}</td>
                        <td class="px-4 py-3 text-slate-600 text-caption">{{ $report->submitted_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-caption text-slate-600">
                            {{ $report->landmark_text ?? ($report->latitude ? number_format($report->latitude, 4) . ', ' . number_format($report->longitude, 4) : '—') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <x-atoms.button size="sm" variant="secondary" wire:click="dismissFlag('{{ $report->id }}')">
                                    {{ __('rapida.redundancy_dismiss') }}
                                </x-atoms.button>
                                <x-atoms.button size="sm" variant="primary" wire:click="keepFlag('{{ $report->id }}')">
                                    {{ __('rapida.redundancy_keep') }}
                                </x-atoms.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">{{ __('rapida.no_reports_yet') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
