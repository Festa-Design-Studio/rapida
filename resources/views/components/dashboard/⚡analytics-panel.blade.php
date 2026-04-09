<?php

use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    public function getTotalReportsProperty(): int
    {
        return DamageReport::count();
    }

    /**
     * @return \Illuminate\Support\Collection<string, int>
     */
    public function getReportsByDamageLevelProperty(): \Illuminate\Support\Collection
    {
        return DamageReport::query()
            ->selectRaw('damage_level, count(*) as count')
            ->groupBy('damage_level')
            ->pluck('count', 'damage_level');
    }

    /**
     * @return \Illuminate\Support\Collection<string, int>
     */
    public function getReportsByInfraTypeProperty(): \Illuminate\Support\Collection
    {
        return DamageReport::query()
            ->selectRaw('infrastructure_type, count(*) as count')
            ->groupBy('infrastructure_type')
            ->pluck('count', 'infrastructure_type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, DamageReport>
     */
    public function getRecentReportsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return DamageReport::query()
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();
    }
};
?>

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Reports --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-body-sm font-medium text-slate-600">Total Reports</p>
            <p class="mt-2 text-3xl font-bold font-heading text-slate-900">{{ $this->totalReports }}</p>
        </div>

        {{-- Minimal --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="inline-block h-3 w-3 rounded-full bg-ground-green-800"></span>
                <p class="text-body-sm font-medium text-slate-600">Minimal</p>
            </div>
            <p class="mt-2 text-3xl font-bold font-heading text-slate-900">{{ $this->reportsByDamageLevel['minimal'] ?? 0 }}</p>
        </div>

        {{-- Partial --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="inline-block h-3 w-3 rounded-full bg-damage-partial-map"></span>
                <p class="text-body-sm font-medium text-slate-600">Partial</p>
            </div>
            <p class="mt-2 text-3xl font-bold font-heading text-slate-900">{{ $this->reportsByDamageLevel['partial'] ?? 0 }}</p>
        </div>

        {{-- Complete --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="inline-block h-3 w-3 rounded-full bg-damage-complete-map"></span>
                <p class="text-body-sm font-medium text-slate-600">Complete</p>
            </div>
            <p class="mt-2 text-3xl font-bold font-heading text-slate-900">{{ $this->reportsByDamageLevel['complete'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Infrastructure type breakdown --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-h4 font-semibold font-heading text-slate-900 mb-4">By Infrastructure Type</h3>
        <ul class="space-y-2">
            @forelse($this->reportsByInfraType as $type => $count)
                <li class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                    <span class="text-body-sm text-slate-600 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                    <span class="text-body-sm font-semibold text-slate-900">{{ $count }}</span>
                </li>
            @empty
                <li class="text-body-sm text-slate-400 py-2">No reports yet.</li>
            @endforelse
        </ul>
    </div>

    {{-- Recent reports table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-h4 font-semibold font-heading text-slate-900">Recent Reports</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead class="bg-surface-page text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">ID</th>
                        <th class="px-4 py-3 font-medium">Damage</th>
                        <th class="px-4 py-3 font-medium">Infrastructure</th>
                        <th class="px-4 py-3 font-medium">Location</th>
                        <th class="px-4 py-3 font-medium">Submitted</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($this->recentReports as $report)
                        <tr class="hover:bg-surface-page/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-caption text-slate-600">{{ Str::limit($report->id, 8, '...') }}</td>
                            <td class="px-4 py-3">
                                <x-atoms.badge :variant="$report->damage_level?->value ?? 'default'">
                                    {{ $report->damage_level?->value ?? 'Unknown' }}
                                </x-atoms.badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600 capitalize">{{ str_replace('_', ' ', $report->infrastructure_type) }}</td>
                            <td class="px-4 py-3 text-slate-600 text-caption">{{ $report->latitude }}, {{ $report->longitude }}</td>
                            <td class="px-4 py-3 text-slate-600 text-caption">{{ $report->submitted_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                @if($report->is_flagged)
                                    <x-atoms.badge variant="partial">Flagged</x-atoms.badge>
                                @else
                                    <x-atoms.badge variant="minimal">Active</x-atoms.badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">No reports submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
