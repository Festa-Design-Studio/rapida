@extends('layouts.rapida')

@section('title', 'Field Dashboard — RAPIDA')

@section('content')
    {{-- Navigation header --}}
    <header class="flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex items-center gap-3">
            <x-atoms.icon name="pin" size="md" class="text-rapida-blue-700" />
            <h1 class="text-h4 font-heading font-bold text-slate-900">RAPIDA</h1>
            <span class="text-body-sm text-slate-500">Field Map</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-body-sm text-slate-600">{{ auth('undp')->user()->name }}</span>
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

    {{-- Full-height map --}}
    <main class="flex flex-col" style="height: calc(100vh - 57px);">
        <livewire:dashboard.field-map />
    </main>
@endsection
