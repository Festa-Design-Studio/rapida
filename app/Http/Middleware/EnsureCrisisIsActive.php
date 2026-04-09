<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCrisisIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $crisis = $request->route('crisis');

        if ($crisis && $crisis->status !== 'active') {
            abort(404, 'This crisis instance is not active.');
        }

        return $next($request);
    }
}
