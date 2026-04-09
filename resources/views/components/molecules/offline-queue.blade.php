@props([
    'pendingCount' => 0,
    'status' => 'online',
])

@php
    $statusLabel = match($status) {
        'online'  => 'Online',
        'offline' => 'Offline',
        'syncing' => 'Syncing...',
        default   => ucfirst($status),
    };

    $statusVariant = match($status) {
        'online'  => 'synced',
        'offline' => 'failed',
        'syncing' => 'pending',
        default   => 'default',
    };
@endphp

<div
    {{ $attributes->class(['flex items-center gap-2']) }}
    role="status"
    aria-live="polite"
    aria-label="Connection status: {{ $statusLabel }}. {{ $pendingCount }} reports pending."
>
    @if($status === 'offline')
        <x-atoms.icon name="cloud-offline" size="sm" />
    @elseif($status === 'syncing')
        <x-atoms.loader variant="spinner" size="sm" message="Syncing" />
    @else
        <x-atoms.icon name="cloud" size="sm" />
    @endif

    <x-atoms.badge variant="{{ $statusVariant }}">{{ $statusLabel }}</x-atoms.badge>

    @if($pendingCount > 0)
        <x-atoms.badge variant="pending">{{ $pendingCount }} pending</x-atoms.badge>
    @endif
</div>
