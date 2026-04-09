<?php

namespace Database\Factories;

use App\Models\Crisis;
use App\Models\Landmark;
use App\Models\UndpUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Landmark>
 */
class LandmarkFactory extends Factory
{
    protected $model = Landmark::class;

    public function definition(): array
    {
        return [
            'crisis_id' => Crisis::factory(),
            'name' => fake()->word(),
            'type' => 'school',
            'latitude' => fake()->latitude(5.4, 5.8),
            'longitude' => fake()->longitude(-0.4, 0.1),
            'added_by' => UndpUser::factory(),
        ];
    }
}
