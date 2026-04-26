<?php

namespace App\Http\Middleware;

use App\Models\Crisis;
use App\Services\ConflictModeService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issues a stable device-scoped UUID cookie so anonymous reporters can
 * re-find their submissions on /my-reports without creating an account.
 *
 * The cookie is the *single source of truth* for the wizard's
 * `device_fingerprint_id` field — read from `request()->cookie(...)` in
 * wizard components, persisted on the report row, and matched again on
 * the re-find page.
 *
 * Privacy posture: if the request resolves to a conflict-context crisis
 * (route-bound, or the active crisis when none is bound) the cookie is
 * not issued. Defense in depth: ConflictModeService already nulls the
 * linkage on the report row, but we also avoid leaving a correlatable
 * UUID in the jar in the first place.
 */
class EnsureDeviceFingerprint
{
    public const COOKIE = 'rapida_device_fingerprint';

    private const TTL_MINUTES = 60 * 24 * 365;

    public function __construct(private readonly ConflictModeService $conflictMode) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->cookie(self::COOKIE)) {
            return $response;
        }

        if ($this->isConflictRequest($request)) {
            return $response;
        }

        $response->headers->setCookie(Cookie::create(
            name: self::COOKIE,
            value: (string) Str::uuid(),
            expire: now()->addMinutes(self::TTL_MINUTES)->getTimestamp(),
            httpOnly: true,
            sameSite: Cookie::SAMESITE_LAX,
        ));

        return $response;
    }

    /**
     * A request is conflict-scoped if its route binds a conflict-context
     * Crisis, or — when no Crisis is bound — the currently active Crisis
     * is conflict-context. Crisis-less requests on a non-conflict
     * deployment fall through to "issue cookie".
     */
    protected function isConflictRequest(Request $request): bool
    {
        $bound = $request->route('crisis');
        if ($bound instanceof Crisis) {
            return $this->conflictMode->isConflict($bound);
        }

        $active = Crisis::where('status', 'active')->first();

        return $active ? $this->conflictMode->isConflict($active) : false;
    }
}
