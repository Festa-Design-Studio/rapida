<?php

namespace App\Providers;

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Policies\CrisisPolicy;
use App\Policies\DamageReportPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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

        $this->configureRateLimiting();
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
