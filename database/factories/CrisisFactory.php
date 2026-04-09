<?php

namespace Database\Factories;

use App\Models\Crisis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Crisis>
 */
class CrisisFactory extends Factory
{
    protected $model = Crisis::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'slug' => fake()->unique()->slug(),
            'default_language' => 'en',
            'available_languages' => ['en', 'fr', 'ar'],
            'active_modules' => [],
            'map_tile_bbox' => null,
            'h3_resolution' => 8,
            'status' => 'active',
        ];
    }
}
