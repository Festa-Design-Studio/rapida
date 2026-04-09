@props([
    'versions' => [],
])

@php
    $defaultVersions = count($versions) > 0 ? $versions : [
        [
            'version' => 3,
            'status' => 'current',
            'editedBy' => 'Ahmed K.',
            'editedAt' => '2026-03-26 14:30',
            'changes' => 'Updated damage level from Partial to Complete after follow-up inspection.',
        ],
        [
            'version' => 2,
            'status' => 'previous',
            'editedBy' => 'Fatima R.',
            'editedAt' => '2026-03-26 10:15',
            'changes' => 'Added infrastructure type and GPS coordinates.',
        ],
        [
            'version' => 1,
            'status' => 'original',
            'editedBy' => 'Ahmed K.',
            'editedAt' => '2026-03-25 08:00',
            'changes' => 'Initial report submitted with photo and location.',
        ],
    ];

    $statusVariant = [
        'current' => 'verified',
        'previous' => 'draft',
        'original' => 'info',
    ];
@endphp

<div
    {{ $attributes->class(['w-full']) }}
    role="region"
    aria-label="Report version history"
>
    <div class="flex items-center gap-2 mb-4">
        <x-atoms.icon name="history" size="md" class="text-slate-500" />
        <h2 class="text-h3 font-heading font-semibold text-slate-900">Version History</h2>
    </div>

    {{-- Timeline --}}
    <div class="relative pl-6" role="list" aria-label="Report versions">
        {{-- Vertical line --}}
        <div class="absolute left-2.5 top-2 bottom-2 w-0.5 bg-slate-200" aria-hidden="true"></div>

        @foreach($defaultVersions as $index => $version)
            @php
                $isFirst = $index === 0;
                $variant = $statusVariant[$version['status']] ?? 'draft';
                $statusLabel = match($version['status']) {
                    'current' => 'Current Version',
                    'previous' => 'Previous',
                    'original' => 'Original',
                    default => ucfirst($version['status']),
                };
            @endphp

            <div
                class="relative pb-6 last:pb-0"
                role="listitem"
                aria-label="Version {{ $version['version'] }}: {{ $statusLabel }}"
            >
                {{-- Timeline dot --}}
                <div class="absolute -left-3.5 mt-1.5 h-3 w-3 rounded-full border-2
                            {{ $isFirst ? 'bg-rapida-blue-700 border-rapida-blue-700' : 'bg-white border-slate-300' }}"
                     aria-hidden="true"></div>

                {{-- Version card --}}
                <div class="ml-4 rounded-lg border {{ $isFirst ? 'border-rapida-blue-100 bg-rapida-blue-50/50' : 'border-slate-200 bg-white' }} p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-body font-semibold text-slate-900">v{{ $version['version'] }}</span>
                            <x-atoms.badge variant="{{ $variant }}">{{ $statusLabel }}</x-atoms.badge>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$isFirst)
                                <x-atoms.button variant="ghost" size="sm" aria-label="View version {{ $version['version'] }}">
                                    View
                                </x-atoms.button>
                                <x-atoms.button variant="ghost" size="sm" aria-label="Restore version {{ $version['version'] }}">
                                    Restore
                                </x-atoms.button>
                            @endif
                        </div>
                    </div>

                    <p class="text-body-sm text-slate-600 mb-2">{{ $version['changes'] }}</p>

                    <div class="flex items-center gap-3 text-caption text-slate-500">
                        <span>{{ $version['editedBy'] }}</span>
                        <span aria-hidden="true">&middot;</span>
                        <time datetime="{{ $version['editedAt'] }}">{{ $version['editedAt'] }}</time>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
