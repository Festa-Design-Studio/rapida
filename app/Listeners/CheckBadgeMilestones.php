<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Services\BadgeService;

class CheckBadgeMilestones
{
    public function __construct(private BadgeService $badgeService) {}

    public function handle(ReportSubmitted $event): void
    {
        $this->badgeService->checkAndAwardBadges($event->report);
    }
}
