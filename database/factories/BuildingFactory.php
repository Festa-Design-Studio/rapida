<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Crisis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'crisis_id' => Crisis::factory(),
            'ms_building_id' => fake()->uuid(),
            'canonical_damage_level' => null,
            'report_count' => 0,
        ];
    }
}
