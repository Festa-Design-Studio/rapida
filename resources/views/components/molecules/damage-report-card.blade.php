@props([
    'photo' => null,
    'damageLevel' => 'minimal',
    'infrastructureType' => null,
    'location' => null,
    'description' => null,
    'reporterName' => null,
    'submittedAt' => null,
    'syncStatus' => 'pending',
    'crisisType' => null,
    'variant' => 'standard',
    'href' => null,
])

@php
    $damageLevelLabel = match($damageLevel) {
        'minimal'  => 'Minimal',
        'partial'  => 'Partial',
        'complete' => 'Complete',
        default    => ucfirst($damageLevel),
    };

    $syncLabel = match($syncStatus) {
        'synced' => 'Synced',
        'pending' => 'Pending sync',
        'failed' => 'Sync failed',
        default => ucfirst($syncStatus),
    };

    $isCompact = $variant === 'compact';
    $photoHeight = $isCompact ? 'h-28' : 'h-40';
@endphp

<article
    {{ $attributes->class([
        'bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md overflow-hidden focus-within:ring-2 focus-within:ring-rapida-blue-700 transition-shadow duration-150',
    ]) }}
    aria-label="Damage report: {{ $damageLevelLabel }} damage at {{ $location }}"
>
    @if($href)<a href="{{ $href }}" class="block">@endif
    {{-- Photo zone --}}
    <div class="relative {{ $photoHeight }} bg-slate-100">
        @if($photo)
            <img src="{{ str_starts_with($photo, 'http') ? $photo : Storage::url($photo) }}" alt="Damage photo at {{ $location }}" class="w-full h-full object-cover" />
        @else
            <div class="w-full h-full flex items-center justify-center text-slate-400">
                <x-atoms.icon name="photo" size="lg" />
            </div>
        @endif
        <div class="absolute bottom-2 left-2">
            <x-atoms.badge variant="{{ $damageLevel }}">{{ $damageLevelLabel }}</x-atoms.badge>
        </div>
    </div>

    {{-- Body --}}
    <div class="p-4 flex flex-col gap-3">
        {{-- Badges row --}}
        <div class="flex flex-wrap items-center gap-2">
            @if($infrastructureType)
                <x-atoms.badge variant="info">{{ $infrastructureType }}</x-atoms.badge>
            @endif
            <x-atoms.badge variant="{{ $syncStatus }}">{{ $syncLabel }}</x-atoms.badge>
        </div>

        {{-- Location --}}
        @if($location)
            <p class="text-body font-medium text-slate-900 truncate">{{ $location }}</p>
        @endif

        {{-- Description --}}
        @if($description && !$isCompact)
            <p class="text-body-sm text-slate-600 line-clamp-2">{{ $description }}</p>
        @endif

        {{-- Metadata row --}}
        <div class="flex items-center justify-between text-caption text-slate-500 border-t border-slate-100 pt-3">
            @if($reporterName)
                <span>{{ $reporterName }}</span>
            @endif
            @if($submittedAt)
                <time datetime="{{ $submittedAt }}">{{ $submittedAt }}</time>
            @endif
        </div>
    </div>
    @if($href)</a>@endif
</article>
