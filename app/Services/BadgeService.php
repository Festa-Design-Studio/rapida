<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Badge;
use App\Models\DamageReport;

class BadgeService
{
    /**
     * @return array<int, string>
     */
    public function checkAndAwardBadges(DamageReport $report): array
    {
        if (! $report->account_id) {
            return []; // Anonymous reporters don't earn badges
        }

        $account = Account::find($report->account_id);
        if (! $account) {
            return [];
        }

        $awarded = [];
        $reportCount = DamageReport::where('account_id', $account->id)
            ->where('crisis_id', $report->crisis_id)
            ->count();

        $milestones = [
            'first_report' => 1,
            'reports_5' => 5,
            'reports_25' => 25,
            'reports_50' => 50,
        ];

        foreach ($milestones as $badgeKey => $threshold) {
            if ($reportCount >= $threshold) {
                $exists = Badge::where('account_id', $account->id)
                    ->where('badge_key', $badgeKey)
                    ->where('crisis_id', $report->crisis_id)
                    ->exists();

                if (! $exists) {
                    Badge::create([
                        'account_id' => $account->id,
                        'badge_key' => $badgeKey,
                        'crisis_id' => $report->crisis_id,
                        'awarded_at' => now(),
                    ]);
                    $awarded[] = $badgeKey;
                }
            }
        }

        if (! empty($awarded)) {
            $account->update(['badge_count' => Badge::where('account_id', $account->id)->count()]);
        }

        return $awarded;
    }
}
