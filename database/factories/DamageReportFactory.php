<?php

namespace Database\Factories;

use App\Enums\CrisisType;
use App\Enums\DamageLevel;
use App\Enums\InfrastructureType;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DamageReport>
 */
class DamageReportFactory extends Factory
{
    protected $model = DamageReport::class;

    public function definition(): array
    {
        return [
            'crisis_id' => Crisis::factory(),
            'damage_level' => fake()->randomElement(DamageLevel::cases())->value,
            'infrastructure_type' => fake()->randomElement(InfrastructureType::cases())->value,
            'crisis_type' => fake()->randomElement(CrisisType::cases())->value,
            'photo_url' => 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            'photo_hash' => fake()->sha256(),
            'latitude' => fake()->latitude(5.4, 5.8),
            'longitude' => fake()->longitude(-0.4, 0.1),
            'location_method' => 'footprint_tap',
            'completeness_score' => fake()->numberBetween(3, 6),
            'submitted_via' => 'web',
            'submitted_at' => fake()->dateTimeBetween('-48 hours', 'now'),
            'is_flagged' => false,
        ];
    }
}
