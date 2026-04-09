<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BackpressureThrottle
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pressure = $this->calculatePressure();

        $request->attributes->set('queue_pressure', $pressure);

        $response = $next($request);

        $response->headers->set('X-Rapida-Queue-Pressure', $pressure);

        return $response;
    }

    private function calculatePressure(): string
    {
        $depth = Cache::remember('queue:depth', 10, function () {
            return DB::table('jobs')->count();
        });

        return match (true) {
            $depth >= 20_000 => 'critical',
            $depth >= 10_000 => 'high',
            $depth >= 5_000 => 'moderate',
            default => 'normal',
        };
    }
}
