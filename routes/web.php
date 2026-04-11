<?php

use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Auth\UndpLoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ReporterController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\EnsureCrisisIsActive;
use App\Http\Middleware\EnsureIsOperator;
use App\Http\Middleware\SetLocaleFromCrisis;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// UNDP Staff Auth
Route::get('/login', [UndpLoginController::class, 'showLoginForm'])->name('undp.login');
Route::post('/login', [UndpLoginController::class, 'login'])->name('undp.login.submit');
Route::post('/logout', [UndpLoginController::class, 'logout'])->name('undp.logout')->middleware('auth:undp');

// UNDP Dashboard (auth required)
Route::middleware('auth:undp')->group(function () {
    Route::get('/dashboard/field', [DashboardController::class, 'field'])->name('dashboard.field');
    Route::get('/dashboard/analyst', [DashboardController::class, 'analyst'])->name('dashboard.analyst');
    Route::post('/dashboard/reports/{report}/flag', [VerificationController::class, 'flag'])->name('reports.flag');
    Route::post('/dashboard/reports/{report}/assign', [VerificationController::class, 'assign'])->name('reports.assign');
    Route::patch('/dashboard/reports/{report}/verify', [VerificationController::class, 'verify'])->name('reports.verify');
    Route::get('/dashboard/export/csv', [ExportController::class, 'csv'])->name('export.csv');
    Route::get('/dashboard/export/geojson', [ExportController::class, 'geojson'])->name('export.geojson');
    Route::get('/dashboard/export/kml', [ExportController::class, 'kml'])->name('export.kml');
    Route::get('/dashboard/export/shapefile', [ExportController::class, 'shapefile'])->name('export.shapefile');
    Route::get('/dashboard/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
});

// Operator Admin Panel
Route::middleware(['auth:undp', EnsureIsOperator::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.crises.index'))->name('index');
    Route::get('/crises', fn () => view('admin.crises'))->name('crises.index');
    Route::get('/landmarks', fn () => view('admin.landmarks'))->name('landmarks.index');
    Route::get('/users', fn () => view('admin.users'))->name('users.index');
});

// All public routes use SetLocaleFromCrisis to respect session locale
Route::middleware(SetLocaleFromCrisis::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('map-home');

    Route::get('/crisis/{crisis:slug}', [ReporterController::class, 'show'])
        ->middleware(EnsureCrisisIsActive::class)
        ->name('crisis.show');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding/language', [OnboardingController::class, 'setLanguage'])->name('onboarding.language');

    Route::get('/submit', function () {
        $crisis = Crisis::where('status', 'active')->first();
        if ($crisis) {
            return redirect()->route('crisis.show', $crisis->slug);
        }

        return redirect()->route('onboarding');
    })->name('submit');

    Route::get('/confirmation', function (Request $request) {
        $report = $request->query('report')
            ? DamageReport::find($request->query('report'))
            : null;

        return view('templates.submission-confirmation', ['report' => $report]);
    })->name('confirmation');

    Route::get('/my-reports', function () {
        $crisis = Crisis::where('status', 'active')->first();
        $reports = $crisis
            ? DamageReport::where('crisis_id', $crisis->id)
                ->latest('submitted_at')
                ->limit(20)
                ->get()
            : collect();

        return view('templates.my-reports', ['reports' => $reports, 'crisis' => $crisis]);
    })->name('my-reports');

    Route::get('/report/{report}', function (DamageReport $report) {
        $report->load(['building', 'verification', 'modules', 'crisis']);

        return view('templates.report-detail', ['report' => $report]);
    })->name('report-detail');
});

Route::get('/dashboard', function () {
    $user = auth('undp')->user();

    return match ($user?->role?->value) {
        'field_coordinator' => redirect()->route('dashboard.field'),
        default => redirect()->route('dashboard.analyst'),
    };
})->middleware('auth:undp')->name('dashboard');
Route::get('/export', fn () => redirect()->route('dashboard.analyst'))->name('export');

// Reporter Account (optional — post-submission)
Route::prefix('account')->name('account.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('/register', [AccountController::class, 'showRegister'])->name('register');
        Route::post('/register', [AccountController::class, 'register'])->name('register.submit');
        Route::get('/login', [AccountController::class, 'showLogin'])->name('login');
        Route::post('/login', [AccountController::class, 'login'])->name('login.submit');
    });
    Route::middleware('auth:web')->group(function () {
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
        Route::delete('/', [AccountController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('rapida-ui')->name('rapida-ui.')->group(function () {
    Route::get('/', fn () => view('rapida-ui.index'))->name('index');

    // Tokens
    Route::get('/tokens/colors', fn () => view('rapida-ui.tokens.colors', ['current' => 'tokens.colors']))->name('tokens.colors');
    Route::get('/tokens/typography', fn () => view('rapida-ui.tokens.typography', ['current' => 'tokens.typography']))->name('tokens.typography');
    Route::get('/tokens/spacing', fn () => view('rapida-ui.tokens.spacing', ['current' => 'tokens.spacing']))->name('tokens.spacing');
    Route::get('/tokens/states', fn () => view('rapida-ui.tokens.states', ['current' => 'tokens.states']))->name('tokens.states');
    Route::get('/tokens/logo', fn () => view('rapida-ui.tokens.logo', ['current' => 'tokens.logo']))->name('tokens.logo');

    // Atoms
    Route::get('/atoms/button', fn () => view('rapida-ui.atoms.button', ['current' => 'atoms.button']))->name('atoms.button');
    Route::get('/atoms/text-input', fn () => view('rapida-ui.atoms.text-input', ['current' => 'atoms.text-input']))->name('atoms.text-input');
    Route::get('/atoms/textarea', fn () => view('rapida-ui.atoms.textarea', ['current' => 'atoms.textarea']))->name('atoms.textarea');
    Route::get('/atoms/photo-upload', fn () => view('rapida-ui.atoms.photo-upload', ['current' => 'atoms.photo-upload']))->name('atoms.photo-upload');
    Route::get('/atoms/select', fn () => view('rapida-ui.atoms.select', ['current' => 'atoms.select']))->name('atoms.select');
    Route::get('/atoms/radio-group', fn () => view('rapida-ui.atoms.radio-group', ['current' => 'atoms.radio-group']))->name('atoms.radio-group');
    Route::get('/atoms/checkbox', fn () => view('rapida-ui.atoms.checkbox', ['current' => 'atoms.checkbox']))->name('atoms.checkbox');
    Route::get('/atoms/toggle', fn () => view('rapida-ui.atoms.toggle', ['current' => 'atoms.toggle']))->name('atoms.toggle');
    Route::get('/atoms/icon', fn () => view('rapida-ui.atoms.icon', ['current' => 'atoms.icon']))->name('atoms.icon');
    Route::get('/atoms/badge', fn () => view('rapida-ui.atoms.badge', ['current' => 'atoms.badge']))->name('atoms.badge');
    Route::get('/atoms/progress-step', fn () => view('rapida-ui.atoms.progress-step', ['current' => 'atoms.progress-step']))->name('atoms.progress-step');
    Route::get('/atoms/loader', fn () => view('rapida-ui.atoms.loader', ['current' => 'atoms.loader']))->name('atoms.loader');

    // Molecules
    Route::get('/molecules/damage-report-card', fn () => view('rapida-ui.molecules.damage-report-card', ['current' => 'molecules.damage-report-card']))->name('molecules.damage-report-card');
    Route::get('/molecules/form-field-group', fn () => view('rapida-ui.molecules.form-field-group', ['current' => 'molecules.form-field-group']))->name('molecules.form-field-group');
    Route::get('/molecules/language-switcher', fn () => view('rapida-ui.molecules.language-switcher', ['current' => 'molecules.language-switcher']))->name('molecules.language-switcher');
    Route::get('/molecules/offline-queue', fn () => view('rapida-ui.molecules.offline-queue', ['current' => 'molecules.offline-queue']))->name('molecules.offline-queue');
    Route::get('/molecules/damage-classification', fn () => view('rapida-ui.molecules.damage-classification', ['current' => 'molecules.damage-classification']))->name('molecules.damage-classification');
    Route::get('/molecules/infrastructure-type', fn () => view('rapida-ui.molecules.infrastructure-type', ['current' => 'molecules.infrastructure-type']))->name('molecules.infrastructure-type');
    Route::get('/molecules/crisis-type', fn () => view('rapida-ui.molecules.crisis-type', ['current' => 'molecules.crisis-type']))->name('molecules.crisis-type');
    Route::get('/molecules/map-pin', fn () => view('rapida-ui.molecules.map-pin', ['current' => 'molecules.map-pin']))->name('molecules.map-pin');
    Route::get('/molecules/notification', fn () => view('rapida-ui.molecules.notification', ['current' => 'molecules.notification']))->name('molecules.notification');
    Route::get('/molecules/submission-confirmation', fn () => view('rapida-ui.molecules.submission-confirmation', ['current' => 'molecules.submission-confirmation']))->name('molecules.submission-confirmation');

    // Organisms
    Route::get('/organisms/submission-wizard', fn () => view('rapida-ui.organisms.submission-wizard', ['current' => 'organisms.submission-wizard']))->name('organisms.submission-wizard');
    Route::get('/organisms/map-organism', fn () => view('rapida-ui.organisms.map-organism', ['current' => 'organisms.map-organism']))->name('organisms.map-organism');
    Route::get('/organisms/navigation-header', fn () => view('rapida-ui.organisms.navigation-header', ['current' => 'organisms.navigation-header']))->name('organisms.navigation-header');
    Route::get('/organisms/community-report-feed', fn () => view('rapida-ui.organisms.community-report-feed', ['current' => 'organisms.community-report-feed']))->name('organisms.community-report-feed');
    Route::get('/organisms/analytics-dashboard', fn () => view('rapida-ui.organisms.analytics-dashboard', ['current' => 'organisms.analytics-dashboard']))->name('organisms.analytics-dashboard');
    Route::get('/organisms/data-export', fn () => view('rapida-ui.organisms.data-export', ['current' => 'organisms.data-export']))->name('organisms.data-export');
    Route::get('/organisms/engagement-panel', fn () => view('rapida-ui.organisms.engagement-panel', ['current' => 'organisms.engagement-panel']))->name('organisms.engagement-panel');
    Route::get('/organisms/report-version-history', fn () => view('rapida-ui.organisms.report-version-history', ['current' => 'organisms.report-version-history']))->name('organisms.report-version-history');

    // Templates
    Route::get('/templates/onboarding', fn () => view('rapida-ui.templates.onboarding', ['current' => 'templates.onboarding']))->name('templates.onboarding');
    Route::get('/templates/map-home', fn () => view('rapida-ui.templates.map-home', ['current' => 'templates.map-home']))->name('templates.map-home');
    Route::get('/templates/submission-wizard', fn () => view('rapida-ui.templates.submission-wizard', ['current' => 'templates.submission-wizard']))->name('templates.submission-wizard');
    Route::get('/templates/submission-confirmation', fn () => view('rapida-ui.templates.submission-confirmation', ['current' => 'templates.submission-confirmation']))->name('templates.submission-confirmation');
    Route::get('/templates/my-reports', fn () => view('rapida-ui.templates.my-reports', ['current' => 'templates.my-reports']))->name('templates.my-reports');
    Route::get('/templates/report-detail', fn () => view('rapida-ui.templates.report-detail', ['current' => 'templates.report-detail']))->name('templates.report-detail');
    Route::get('/templates/analytics-dashboard', fn () => view('rapida-ui.templates.analytics-dashboard', ['current' => 'templates.analytics-dashboard']))->name('templates.analytics-dashboard');
    Route::get('/templates/data-export', fn () => view('rapida-ui.templates.data-export', ['current' => 'templates.data-export']))->name('templates.data-export');
    Route::get('/templates/pitch-video', fn () => view('rapida-ui.templates.pitch-video', ['current' => 'templates.pitch-video']))->name('templates.pitch-video');
});

// Temporary test route — remove after debugging
Route::get('/submit-test', fn () => view('templates.submit-test'))->name('submit-test');
