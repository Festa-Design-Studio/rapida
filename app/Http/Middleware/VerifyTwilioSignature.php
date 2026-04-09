<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class VerifyTwilioSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip verification in local/testing environments
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        $authToken = config('services.twilio.auth_token');
        if (! $authToken) {
            abort(500, 'Twilio auth token not configured.');
        }

        $validator = new RequestValidator($authToken);

        $valid = $validator->validate(
            $request->header('X-Twilio-Signature', ''),
            $request->fullUrl(),
            $request->all()
        );

        if (! $valid) {
            abort(403, 'Invalid Twilio signature.');
        }

        return $next($request);
    }
}
