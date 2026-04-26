@props([
    'h3CellId',
    'severity' => 'caution',
    'note' => null,
    'conflictContext' => false,
])

@php
    // Trauma-informed: never alarming. Calm crisis-rose tones for the banner;
    // the severity word ("caution"/"warning"/"critical") comes from a lang
    // key so translators can soften per-locale.
    $severityKey = in_array($severity, ['caution', 'warning', 'critical'], true) ? $severity : 'caution';
@endphp

{{-- Conflict-mode crises must never surface a danger zone (see DangerZoneService). --}}
@if(! $conflictContext)
<div
    x-data="{
        visible: !localStorage.getItem('rapida_danger_dismissed_{{ $h3CellId }}'),
        dismiss() {
            localStorage.setItem('rapida_danger_dismissed_{{ $h3CellId }}', 'true');
            this.visible = false;
        }
    }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-calm"
    x-transition:enter-start="translate-y-2 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-fast"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->class([
        'w-full rounded-xl p-padding-card border border-crisis-rose-100 bg-crisis-rose-50',
        'motion-reduce:transition-none',
    ]) }}
    role="status"
    aria-live="polite"
>
    <div class="flex items-start gap-3">
        <div class="flex-1 min-w-0 space-y-nano">
            <p class="text-label font-medium text-crisis-rose-900">
                {{ __("rapida.danger_zone_{$severityKey}_label") }}
            </p>
            @if($note)
                <p class="text-body-sm text-text-primary">{{ $note }}</p>
            @else
                <p class="text-body-sm text-text-primary">{{ __('rapida.danger_zone_default_body') }}</p>
            @endif
            <p class="text-caption text-crisis-rose-700">{{ __('rapida.danger_zone_field_team_note') }}</p>
        </div>
        <button
            type="button"
            @click="dismiss()"
            class="shrink-0 text-crisis-rose-700 hover:text-crisis-rose-900 transition-colors"
            aria-label="{{ __('rapida.dismiss') }}"
        >
            <x-atoms.icon name="x" size="sm" />
        </button>
    </div>
</div>
@endif
