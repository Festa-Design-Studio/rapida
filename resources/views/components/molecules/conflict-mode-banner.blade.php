@props([
    'show' => false,
])

@if($show)
    <div
        {{ $attributes->class([
            'w-full px-4 py-3 bg-rapida-blue-50 border-b border-rapida-blue-100',
            'flex items-center justify-center gap-2',
        ]) }}
        role="status"
        aria-live="polite"
    >
        <svg class="h-4 w-4 text-rapida-blue-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <p class="text-body-sm text-rapida-blue-900">{{ __('rapida.conflict_mode_banner') }}</p>
    </div>
@endif
