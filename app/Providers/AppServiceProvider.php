<?php

namespace App\Providers;

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Policies\CrisisPolicy;
use App\Policies\DamageReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(DamageReport::class, DamageReportPolicy::class);
        Gate::policy(Crisis::class, CrisisPolicy::class);
    }
}
