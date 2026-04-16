<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Badge;
use App\Models\Crisis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'badge_key' => fake()->randomElement(['first_report', 'five_reports', 'ten_reports', 'verified_reporter', 'crisis_responder']),
            'crisis_id' => Crisis::factory(),
            'awarded_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
