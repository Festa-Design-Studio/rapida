<?php

namespace App\Http\Controllers;

use App\Models\Crisis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        $crisis = Crisis::where('status', 'active')->first();
        $languages = $crisis?->available_languages ?? ['en', 'fr', 'ar', 'es', 'ru', 'zh'];

        $languageNames = [
            'en' => 'English',
            'fr' => 'Français',
            'es' => 'Español',
            'ar' => 'العربية',
            'ru' => 'Русский',
            'zh' => '中文',
        ];

        $availableLanguages = collect($languages)->mapWithKeys(fn ($code) => [
            $code => $languageNames[$code] ?? $code,
        ])->all();

        return view('templates.onboarding', compact('crisis', 'availableLanguages'));
    }

    public function setLanguage(Request $request): RedirectResponse
    {
        $lang = $request->input('language', 'en');
        session(['locale' => $lang]);
        app()->setLocale($lang);

        $crisis = Crisis::where('status', 'active')->first();
        if ($crisis) {
            return redirect()->route('crisis.show', $crisis->slug);
        }

        return redirect()->route('map-home');
    }
}
