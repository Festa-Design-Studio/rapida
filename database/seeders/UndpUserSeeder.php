<?php

namespace Database\Seeders;

use App\Models\UndpUser;
use Illuminate\Database\Seeder;

class UndpUserSeeder extends Seeder
{
    public function run(): void
    {
        UndpUser::create([
            'name' => 'Abayomi Ogundipe',
            'email' => 'abayomi@rapida.app',
            'password' => 'password',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        // UNDP evaluator account for demo review
        UndpUser::create([
            'name' => 'UNDP Evaluator',
            'email' => 'evaluator@undp.org',
            'password' => 'rapida-demo-2026',
            'role' => 'analyst',
            'is_active' => true,
        ]);
    }
}
