<?php

namespace Database\Factories;

use App\Models\UndpUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UndpUser>
 */
class UndpUserFactory extends Factory
{
    protected $model = UndpUser::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'analyst',
            'is_active' => true,
        ];
    }
}
