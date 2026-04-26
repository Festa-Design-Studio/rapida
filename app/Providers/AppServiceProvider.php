<?php

namespace App\Providers;

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\Landmark;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use App\Policies\CrisisPolicy;
use App\Policies\DamageReportPolicy;
use App\Policies\LandmarkPolicy;
use App\Policies\RecoveryOutcomePolicy;
use App\Policies\UndpUserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(DamageReport::class, DamageReportPolicy::class);
        Gate::policy(Crisis::class, CrisisPolicy::class);
        Gate::policy(Landmark::class, LandmarkPolicy::class);
        Gate::policy(RecoveryOutcome::class, RecoveryOutcomePolicy::class);
        Gate::policy(UndpUser::class, UndpUserPolicy::class);

        $this->configureRateLimiting();
        $this->configureHealthChecks();
        $this->configureGlobalLanguageMenu();
    }

    /**
     * Share a crisis-aware language map with every rendered view so both
     * the main layout (floating pill on crisis.show) and anonymous Blade
     * components (navigation-header organism) see the same list. A
     * wildcard composer is required because x-components have isolated
     * variable scopes — variables set on the layout view do not cascade
     * into their descendants. This composer runs once per view render
     * and is cheap: one route lookup + one collect() per render.
     */
    private function configureGlobalLanguageMenu(): void
    {
        View::composer('*', function ($view) {
            $crisis = request()->route('crisis');
            $codes = $crisis instanceof Crisis && ! empty($crisis->available_languages)
                ? $crisis->available_languages
                : config('app.supported_locales', ['en']);

            $names = config('app.language_names', []);
            $menu = collect($codes)
                ->mapWithKeys(fn (string $code) => [$code => $names[$code] ?? $code])
                ->all();

            $view->with('__rapidaLanguageMenu', $menu);
        });
    }

    private function configureHealthChecks(): void
    {
        Health::checks([
            DatabaseCheck::new(),
            UsedDiskSpaceCheck::new()->warnWhenUsedSpaceIsAbovePercentage(80)->failWhenUsedSpaceIsAbovePercentage(90),
        ]);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('rapida-report', function (Request $request) {
            $key = $request->input('device_fingerprint_id', $request->ip());

            return [
                Limit::perMinute(10)->by($key),
                Limit::perHour(30)->by($key),
                Limit::perDay(100)->by($key),
            ];
        });

        RateLimiter::for('rapida-photo', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perHour(50)->by($request->ip()),
            ];
        });

        RateLimiter::for('rapida-pins', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('rapida-export', function (Request $request) {
            $key = $request->user('undp')?->id ?? $request->ip();

            return Limit::perHour(5)->by($key);
        });

        RateLimiter::for('rapida-whatsapp', function (Request $request) {
            $phone = $request->input('From', $request->ip());

            return [
                Limit::perMinute(10)->by($phone),
                Limit::perHour(60)->by($phone),
            ];
        });

        RateLimiter::for('rapida-global', function (Request $request) {
            $slug = $request->route('slug', 'global');

            return Limit::perMinute(500)->by($slug)->response(function () {
                return response()->json([
                    'message' => __('rapida.rate_limit_global'),
                    'queued' => true,
                    'retry_after' => 30,
                ], 429);
            });
        });
    }
}
