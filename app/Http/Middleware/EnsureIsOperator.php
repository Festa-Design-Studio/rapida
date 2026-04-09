<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = auth('undp')->user()?->role?->value;

        if (! in_array($role, ['operator', 'superadmin'])) {
            abort(403, 'Operator or Superadmin access required.');
        }

        return $next($request);
    }
}
