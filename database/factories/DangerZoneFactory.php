<?php

namespace Database\Factories;

use App\Models\Crisis;
use App\Models\DangerZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DangerZone>
 */
class DangerZoneFactory extends Factory
{
    protected $model = DangerZone::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crisis_id' => Crisis::factory(),
            'h3_cell_id' => '882a10c'.fake()->bothify('?##').'fffff',
            'severity' => fake()->randomElement(['caution', 'warning', 'critical']),
            'note' => fake()->optional()->sentence(),
            'created_by' => null,
            'expires_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }

    public function expiring(int $hours = 24): static
    {
        return $this->state(fn () => ['expires_at' => now()->addHours($hours)]);
    }
}
