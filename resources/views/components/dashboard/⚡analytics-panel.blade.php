<?php

use App\Models\DamageReport;
use Livewire\Component;

new class extends Component
{
    public string $crisisId = '';

    public function mount(): void
    {
        $crisis = \App\Models\Crisis::where('status', 'active')->first();
        $this->crisisId = $crisis?->id ?? '';
    }

    public function getTotalReportsProperty(): int
    {
        $query = DamageReport::query();

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->count();
    }

    /**
     * @return \Illuminate\Support\Collection<string, int>
     */
    public function getReportsByDamageLevelProperty(): \Illuminate\Support\Collection
    {
        $query = DamageReport::query()
            ->selectRaw('damage_level, count(*) as count')
            ->groupBy('damage_level');

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->pluck('count', 'damage_level');
    }

    /**
     * @return \Illuminate\Support\Collection<string, int>
     */
    public function getReportsByInfraTypeProperty(): \Illuminate\Support\Collection
    {
        $query = DamageReport::query()
            ->selectRaw('infrastructure_type, count(*) as count')
            ->groupBy('infrastructure_type');

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->pluck('count', 'infrastructure_type');
    }

    public function getReportsByDayProperty(): \Illuminate\Support\Collection
    {
        $query = DamageReport::query()
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(14);

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Building>
     */
    public function getTopBuildingsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $query = \App\Models\Building::where('report_count', '>', 0)
            ->orderByDesc('report_count')
            ->limit(10);

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, DamageReport>
     */
    public function getRecentReportsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $query = DamageReport::query()
            ->orderByDesc('submitted_at')
            ->limit(10);

        if ($this->crisisId) {
            $query->where('crisis_id', $this->crisisId);
        }

        return $query->get();
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

    {{-- Reports over time --}}
    @if($this->reportsByDay->count() > 0)
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-h4 font-semibold font-heading text-slate-900 mb-4">{{ __('rapida.reports_over_time') }}</h3>
        @php $maxCount = $this->reportsByDay->max('count') ?: 1; @endphp
        <div class="flex items-end gap-1" style="height: 120px;">
            @foreach($this->reportsByDay as $day)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-caption text-text-placeholder">{{ $day->count }}</span>
                    <div class="w-full rounded-t bg-rapida-blue-500 hover:bg-rapida-blue-700 transition-colors"
                         style="height: {{ max(4, ($day->count / $maxCount) * 100) }}px"
                         title="{{ $day->date }}: {{ $day->count }} reports"></div>
                    <span class="text-[9px] text-text-placeholder truncate w-full text-center">
                        {{ \Carbon\Carbon::parse($day->date)->format('M d') }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

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

    {{-- Top reported buildings --}}
    @if($this->topBuildings->count() > 0)
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-h4 font-semibold font-heading text-slate-900">{{ __('rapida.top_buildings') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-body-sm text-left">
                <thead class="bg-surface-page text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">{{ __('rapida.building_id') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('rapida.reports_count') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('rapida.damage_level_label') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('rapida.last_updated') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($this->topBuildings as $index => $building)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-caption text-slate-600">{{ Str::limit($building->ms_building_id ?? $building->id, 16, '...') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $building->report_count }}</td>
                            <td class="px-4 py-3">
                                @if($building->canonical_damage_level)
                                    <x-atoms.badge :variant="$building->canonical_damage_level">
                                        {{ ucfirst($building->canonical_damage_level) }}
                                    </x-atoms.badge>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-caption text-slate-600">{{ $building->last_updated_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
