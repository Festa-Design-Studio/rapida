@extends('layouts.rapida')

@section('title', 'UNDP Staff Login — RAPIDA')

@section('content')
<div class="min-h-screen flex items-center justify-center px-padding-page-x-mob">
    <div class="w-full max-w-sm">
        <div class="text-center mb-gap-component">
            <div class="flex justify-center mb-inner">
                <x-atoms.logo size="xl" variant="mark" class="text-rapida-blue-700" />
            </div>
            <h1 class="text-h2 font-heading font-semibold text-rapida-blue-900">{{ __('rapida.app_name') }}</h1>
            <p class="text-body-sm text-text-secondary mt-nano">UNDP Staff Login</p>
        </div>

        <form method="POST" action="{{ route('undp.login.submit') }}" class="bg-surface-card rounded-lg p-padding-card shadow-sm border border-grey-100 space-y-element">
            @csrf

            <x-atoms.text-input
                name="email"
                label="Email"
                type="email"
                placeholder="you@undp.org"
                :required="true"
                :error="$errors->first('email')"
                :value="old('email')"
            />

            <x-atoms.text-input
                name="password"
                label="Password"
                type="password"
                :required="true"
                :error="$errors->first('password')"
            />

            <div class="flex items-center gap-micro">
                <x-atoms.checkbox name="remember" label="Remember me" />
            </div>

            <x-atoms.button type="submit" variant="primary" size="lg" class="w-full">
                Sign in
            </x-atoms.button>
        </form>
    </div>
</div>
@endsection
