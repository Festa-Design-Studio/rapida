<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyInternalSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.ai.secret');

        if (! $secret || $request->header('X-Internal-Secret') !== $secret) {
            abort(403, 'Invalid internal secret.');
        }

        return $next($request);
    }
}
