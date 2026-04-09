<?php

namespace Database\Factories;

use App\Models\Crisis;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecoveryOutcome>
 */
class RecoveryOutcomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'crisis_id' => Crisis::factory(),
            'h3_cell_id' => $this->faker->regexify('[0-9a-f]{15}'),
            'message' => $this->faker->sentence(12),
            'triggered_by' => UndpUser::factory(),
            'triggered_at' => now(),
        ];
    }
}
