@extends('layouts.rapida')

@section('title', 'Field Dashboard — RAPIDA')

@section('content')
    {{-- Navigation header. flex-wrap + truncated name on mobile prevents
         the "FieldAbayomiSuperadminLogou" jam observed in the screenshot
         at 375px viewport. Layout escalates from 2-line stacked on tiny
         screens to a single line on tablet+. --}}
    <header class="flex flex-wrap items-center justify-between gap-y-2 gap-x-3 bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex items-center gap-2 min-w-0">
            <x-atoms.logo size="sm" variant="full" class="text-rapida-blue-700 shrink-0" />
            <span class="text-body-sm text-text-secondary truncate">{{ __('rapida.dashboard_field') }}</span>
        </div>
        <div class="flex items-center gap-2 min-w-0">
            <span class="hidden sm:inline text-body-sm text-slate-600 truncate max-w-[120px]">{{ auth('undp')->user()->name }}</span>
            <x-atoms.badge variant="{{ match(auth('undp')->user()->role->value) {
                'superadmin', 'operator' => 'verified',
                'analyst' => 'synced',
                'field_coordinator' => 'pending',
                default => 'default',
            } }}">
                {{ ucwords(str_replace('_', ' ', auth('undp')->user()->role->value)) }}
            </x-atoms.badge>
            <form method="POST" action="{{ route('undp.logout') }}">
                @csrf
                <x-atoms.button type="submit" variant="ghost" size="sm">
                    Logout
                </x-atoms.button>
            </form>
        </div>
    </header>

    <x-dashboard.dashboard-nav current="field" />

    {{-- Full-height map --}}
    <main class="flex flex-col" style="height: calc(100vh - 100px);">
        <livewire:dashboard.field-map />
    </main>
@endsection
