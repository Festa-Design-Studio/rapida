@props(['current' => 'analyst'])

@php
    $role = auth('undp')->user()?->role?->value;
    $isOperator = in_array($role, ['operator', 'superadmin']);
@endphp

<nav class="bg-white border-b border-slate-200 px-2 sm:px-4 md:px-6">
    {{-- All tabs live in a single flex group with horizontal scroll on
         overflow. Admin Panel lives in the same scroll container as the
         primary tabs so scroll order matches visual order — placing it
         outside justify-between caused it to disappear off-screen first. --}}
    <div class="max-w-7xl mx-auto flex items-center gap-1 overflow-x-auto">
        @foreach([
            'field' => ['label' => __('rapida.dashboard_field'), 'route' => 'dashboard.field'],
            'analyst' => ['label' => __('rapida.dashboard_analyst'), 'route' => 'dashboard.analyst'],
        ] as $key => $item)
            <a href="{{ route($item['route']) }}"
               class="px-3 sm:px-4 py-3 text-body-sm font-medium border-b-2 transition-colors whitespace-nowrap shrink-0
                      {{ $current === $key
                          ? 'border-rapida-blue-700 text-rapida-blue-900'
                          : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
               @if($current === $key) aria-current="page" @endif
            >
                {{ $item['label'] }}
            </a>
        @endforeach
        @if($isOperator)
            <a href="{{ route('admin.index') }}"
               class="ms-auto px-3 sm:px-4 py-3 text-body-sm font-medium text-slate-500 hover:text-rapida-blue-700 transition-colors whitespace-nowrap shrink-0">
                Admin Panel
            </a>
        @endif
    </div>
</nav>
