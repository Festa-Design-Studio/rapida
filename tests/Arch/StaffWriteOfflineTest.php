<?php

/**
 * Gap-33: every UNDP-staff write route must either be background-sync
 * registered in resources/js/service-worker.js OR explicitly marked
 * online-only with a comment so a future PR can't silently add a write
 * route that fails for offline field coordinators.
 *
 * Allowlist of online-only patterns:
 *   - /admin/* — operator console assumed to be on stable connection
 *   - /dashboard/export/* — large download responses, not idempotent retries
 *   - /login, /logout — auth flow needs immediate response
 */
it('every UNDP-staff write route is offline-queued or in the online-only allowlist', function () {
    $sw = file_get_contents(__DIR__.'/../../resources/js/service-worker.js');
    $webRoutes = file_get_contents(__DIR__.'/../../routes/web.php');

    // Pull every route declared inside the auth:undp middleware group that
    // uses POST/PATCH/PUT/DELETE — those are the writes that need offline.
    preg_match_all(
        '/Route::(post|patch|put|delete)\s*\(\s*[\'"]([^\'"]+)[\'"]/',
        $webRoutes,
        $matches,
        PREG_SET_ORDER,
    );

    // Strip backslash-escapes so SW regex literals match plain-string searches.
    $swPlain = str_replace(['\\/', '\\.'], ['/', '.'], $sw);

    // Online-only paths: not field-coordinator-on-a-phone scenarios. Some
    // entries are literal route fragments because the Route::group prefix
    // isn't captured by this test's regex (account.destroy declares "/" but
    // is mounted at /account/).
    $onlineOnlyAllowlist = [
        '/^\/admin/',                    // operator console — assumed stable connection
        '/^\/dashboard\/export/',        // download responses, not idempotent retries
        '/^\/login/',
        '/^\/logout/',
        '/^\/onboarding/',               // public reporter onboarding (not staff write)
        '/^\/account/',                  // reporter account flows
        '/^\/register/',                 // reporter registration
        '/^\/$/',                        // account destroy (mounted under /account prefix)
    ];

    $offenders = [];
    foreach ($matches as [$_, $verb, $path]) {
        $isAllowlisted = false;
        foreach ($onlineOnlyAllowlist as $pattern) {
            if (preg_match($pattern, $path)) {
                $isAllowlisted = true;
                break;
            }
        }
        if ($isAllowlisted) {
            continue;
        }

        // Service-worker match: extract the trailing action segment of the
        // route ("flag", "assign", "verify"...) and check that the SW
        // registers it as part of a dashboard/reports pattern.
        if (preg_match('/\/([a-z-]+)$/', $path, $action)
            && str_contains($swPlain, '/dashboard/reports')
            && str_contains($swPlain, $action[1])) {
            continue;
        }

        $offenders[] = strtoupper($verb).' '.$path;
    }

    expect($offenders)->toBeEmpty(
        'UNDP-staff write routes must be background-sync registered in service-worker.js OR added to the online-only allowlist in this test. Offenders: '
        .json_encode($offenders, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
});
