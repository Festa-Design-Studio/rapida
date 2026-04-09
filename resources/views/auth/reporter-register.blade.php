@extends('layouts.rapida')

@section('title', __('account.register_title') . ' — RAPIDA')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-surface-page">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <x-atoms.icon name="badge-verified" size="xl" class="text-rapida-blue-700 mx-auto" />
            <h1 class="text-h2 font-heading font-semibold text-slate-900 mt-4">{{ __('account.register_title') }}</h1>
            <p class="text-body-sm text-slate-500 mt-2">{{ __('account.register_desc') }}</p>
        </div>

        <form method="POST" action="{{ route('account.register.submit') }}" class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 space-y-5">
            @csrf

            @if($reportId)
                <input type="hidden" name="report_id" value="{{ $reportId }}" />
                <div class="rounded-lg bg-rapida-blue-50 border border-rapida-blue-100 p-3 text-center">
                    <p class="text-body-sm text-rapida-blue-900">{{ __('account.link_report') }}</p>
                </div>
            @endif

            <x-atoms.text-input
                name="email"
                label="{{ __('account.email_label') }}"
                type="email"
                placeholder="{{ __('account.email_placeholder') }}"
                :required="true"
                :error="$errors->first('email')"
                :value="old('email')"
            />

            <x-atoms.text-input
                name="password"
                label="{{ __('account.password_label') }}"
                type="password"
                placeholder="{{ __('account.password_placeholder') }}"
                :required="true"
                :error="$errors->first('password')"
            />

            <x-atoms.text-input
                name="password_confirmation"
                label="{{ __('account.password_confirm_label') }}"
                type="password"
                :required="true"
            />

            <p class="text-caption text-slate-400">{{ __('account.privacy_note') }}</p>

            <x-atoms.button type="submit" variant="primary" size="lg" class="w-full">
                {{ __('account.btn_register') }}
            </x-atoms.button>
        </form>

        <p class="text-center text-body-sm text-slate-500 mt-6">
            {{ __('account.already_have_account') }}
            <a href="{{ route('account.login') }}" class="font-medium text-rapida-blue-700 hover:text-rapida-blue-900">{{ __('account.btn_login') }}</a>
        </p>
    </div>
</div>
@endsection
