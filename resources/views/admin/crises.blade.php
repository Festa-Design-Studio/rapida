@extends('layouts.rapida')

@section('title', 'Crisis Management — RAPIDA Admin')

@section('content')
<div class="min-h-screen bg-surface-page">
    <x-organisms.navigation-header currentRoute="admin" />
    <x-admin.admin-nav current="crises" />
    <main class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-h1 font-heading font-bold text-slate-900 mb-6">Crisis Management</h1>
        <livewire:admin.crisis-manager />
    </main>
</div>
@endsection
