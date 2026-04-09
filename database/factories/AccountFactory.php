<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Crisis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'phone_or_email_hash' => hash('sha256', fake()->unique()->safeEmail()),
            'crisis_id' => Crisis::factory(),
            'preferred_language' => 'en',
            'badge_count' => 0,
            'leaderboard_score' => 0,
        ];
    }
}
