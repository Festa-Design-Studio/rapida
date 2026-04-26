<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Gap-52: thrown by ReportSubmissionService when the per-building
 * anti-gaming rule fires (PRD V2 §3.5: one report per
 * building_footprint_id + account_id per 24-hour window).
 *
 * Carries the localised user-facing message in $this->getMessage().
 * Controllers catch this and return a 429 with the message body so
 * reporters see "You've already submitted a report for this building
 * today. Thank you for staying engaged." in their language.
 */
class ReportRateLimitedException extends RuntimeException
{
    public function __construct(string $message, public readonly string $reason = 'building_rate_limit')
    {
        parent::__construct($message);
    }
}
