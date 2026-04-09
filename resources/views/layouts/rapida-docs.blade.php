@extends('layouts.rapida')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen">

    {{-- Mobile sidebar toggle --}}
    <div class="lg:hidden sticky top-0 z-30 bg-surface-page border-b border-grey-100 px-4 py-3 flex items-center justify-between">
        <a href="{{ route('rapida-ui.index') }}" class="text-h4 font-heading font-semibold text-rapida-blue-900">RAPIDA UI</a>
        <button @click="sidebarOpen = !sidebarOpen" class="h-12 w-12 flex items-center justify-center rounded-lg hover:bg-slate-50" aria-label="Toggle navigation">
            <svg class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="flex">
        {{-- Sidebar --}}
        <aside
            x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:sticky lg:translate-x-0 top-0 lg:top-0 left-0 z-40 lg:z-auto
                   w-64 h-screen overflow-y-auto
                   bg-surface-page border-r border-grey-100
                   p-padding-card pt-section
                   transition-transform duration-200 lg:transition-none"
            @click.outside="sidebarOpen = false"
        >
            <x-docs.sidebar :current="$current ?? ''" />
        </aside>

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-black/20 lg:hidden" @click="sidebarOpen = false"></div>

        {{-- Content --}}
        <main class="flex-1 min-w-0 px-padding-page-x-mob md:px-padding-page-x-tab lg:px-10 py-section max-w-4xl">
            @yield('docs-content')
        </main>
    </div>
</div>
@endsection
