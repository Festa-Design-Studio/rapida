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

        // Use the public URL that Twilio signed against, not the internal
        // proxy URL. Behind reverse proxies (Laravel Cloud, Cloudflare),
        // fullUrl() may return http:// while Twilio signed https://.
        $url = $request->fullUrl();
        if (! str_starts_with($url, 'https://') && $request->secure()) {
            $url = 'https://'.substr($url, 7);
        } elseif (! str_starts_with($url, 'https://') && config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            $url = preg_replace('/^http:/', 'https:', $url);
        }

        $valid = $validator->validate(
            $request->header('X-Twilio-Signature', ''),
            $url,
            $request->all()
        );

        if (! $valid) {
            abort(403, 'Invalid Twilio signature.');
        }

        return $next($request);
    }
}
