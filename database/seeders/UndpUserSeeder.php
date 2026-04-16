<?php

namespace Database\Seeders;

use App\Models\UndpUser;
use Illuminate\Database\Seeder;

class UndpUserSeeder extends Seeder
{
    public function run(): void
    {
        UndpUser::firstOrCreate(['email' => 'abayomi@rapida.app'], [
            'name' => 'Abayomi Ogundipe',
            'email' => 'abayomi@rapida.app',
            'password' => 'password',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        // UNDP evaluator account for demo review
        UndpUser::firstOrCreate(['email' => 'evaluator@undp.org'], [
            'name' => 'UNDP Evaluator',
            'password' => 'rapida-demo-2026',
            'role' => 'analyst',
            'is_active' => true,
        ]);
    }
}
