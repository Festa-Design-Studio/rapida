@extends('layouts.rapida')

@section('title', $crisis->name . ' — Report Damage')

@section('content')
    <livewire:wizard.wizard-shell :crisis="$crisis" />
@endsection
