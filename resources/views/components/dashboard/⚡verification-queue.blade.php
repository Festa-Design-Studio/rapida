<?php

use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, DamageReport>
     */
    public function getReportsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return DamageReport::query()
            ->where(function ($q) {
                $q->where('is_flagged', true)
                    ->orWhereHas('verification', function ($vq) {
                        $vq->whereIn('status', ['pending', 'in_field']);
                    });
            })
            ->with('verification')
            ->orderByDesc('submitted_at')
            ->get();
    }

    public function flag(string $reportId): void
    {
        DamageReport::where('id', $reportId)->update(['is_flagged' => true]);
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
        $report?->verification?->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
        $report?->update(['is_flagged' => false]);
    }

    public function dispute(string $reportId): void
    {
        $report = DamageReport::find($reportId);
        $report?->verification?->update([
            'status' => 'disputed',
        ]);
    }
};
?>

<div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200">
        <h3 class="text-h4 font-semibold font-heading text-slate-900">Verification Queue</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-body-sm text-left">
            <thead class="bg-surface-page text-slate-600">
                <tr>
                    <th class="px-4 py-3 font-medium">ID</th>
                    <th class="px-4 py-3 font-medium">Damage</th>
                    <th class="px-4 py-3 font-medium">{{ __('rapida.ai_confidence_label') }}</th>
                    <th class="px-4 py-3 font-medium">Infrastructure</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Submitted</th>
                    <th class="px-4 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($this->reports as $report)
                    <tr class="hover:bg-surface-page/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-caption text-slate-600">{{ Str::limit($report->id, 8, '...') }}</td>
                        <td class="px-4 py-3">
                            <x-atoms.badge :variant="$report->damage_level?->value ?? 'default'">
                                {{ $report->damage_level?->value ?? 'Unknown' }}
                            </x-atoms.badge>
                        </td>
                        <td class="px-4 py-3">
                            @if($report->ai_confidence !== null)
                                @php
                                    $confidencePercent = round($report->ai_confidence * 100);
                                    $confidenceTier = $report->ai_confidence > 0.85 ? 'high' : ($report->ai_confidence >= 0.60 ? 'medium' : 'low');
                                @endphp
                                <x-atoms.badge :variant="'confidence-' . $confidenceTier">{{ $confidencePercent }}%</x-atoms.badge>
                            @else
                                <span class="text-slate-400 text-caption">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $report->infrastructure_type) }}</td>
                        <td class="px-4 py-3">
                            @if($report->verification)
                                <x-atoms.badge variant="default">{{ str_replace('_', ' ', $report->verification->status->value ?? $report->verification->status) }}</x-atoms.badge>
                            @elseif($report->is_flagged)
                                <x-atoms.badge variant="partial">Flagged</x-atoms.badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-caption">{{ $report->submitted_at?->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <x-atoms.button size="sm" variant="primary" wire:click="assign('{{ $report->id }}')">
                                    Assign
                                </x-atoms.button>
                                <x-atoms.button size="sm" variant="primary" class="bg-ground-green-800 hover:bg-ground-green-700 focus:ring-ground-green-800" wire:click="verify('{{ $report->id }}')">
                                    Verify
                                </x-atoms.button>
                                <x-atoms.button size="sm" variant="secondary" wire:click="dispute('{{ $report->id }}')">
                                    Dispute
                                </x-atoms.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">No reports pending verification.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
