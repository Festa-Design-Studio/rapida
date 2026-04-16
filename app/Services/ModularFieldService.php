<?php

namespace App\Services;

use App\Models\DamageReport;
use App\Models\ReportModule;

class ModularFieldService
{
    /**
     * @param  array<string, mixed>  $responses
     */
    public function storeResponses(DamageReport $report, array $responses): void
    {
        $moduleKeyMap = [
            'electricity_condition' => ['electricity', 'condition'],
            'health_functioning' => ['health', 'functioning'],
            'pressing_needs_needs' => ['pressing_needs', 'needs'],
        ];

        foreach ($responses as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            if (! isset($moduleKeyMap[$key])) {
                continue;
            }

            [$moduleKey, $fieldKey] = $moduleKeyMap[$key];

            ReportModule::create([
                'report_id' => $report->id,
                'module_key' => $moduleKey,
                'field_key' => $fieldKey,
                'value' => is_array($value) ? $value : [$value],
            ]);
        }
    }
}
