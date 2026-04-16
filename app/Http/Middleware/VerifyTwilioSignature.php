<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Behind reverse proxies (Laravel Cloud / Envoy / Cloudflare),
        // $request->fullUrl() returns http:// internally while Twilio
        // signed against the public https:// URL. Reconstruct the exact
        // URL Twilio used by forcing HTTPS when behind a proxy.
        $scheme = $request->header('X-Forwarded-Proto', $request->getScheme());
        $url = $scheme.'://'.$request->getHost().$request->getRequestUri();

        $valid = $validator->validate(
            $request->header('X-Twilio-Signature', ''),
            $url,
            $request->all()
        );

        if (! $valid) {
            // Log for debugging, then reject
            Log::warning('Twilio signature validation failed', [
                'reconstructed_url' => $url,
                'full_url' => $request->fullUrl(),
                'forwarded_proto' => $request->header('X-Forwarded-Proto'),
                'scheme' => $request->getScheme(),
            ]);
            abort(403, 'Invalid Twilio signature.');
        }

        return $next($request);
    }
}
