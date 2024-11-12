<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

use function Laravel\Prompts\info;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Luca Castelnuovo',
            'email' => 'luca@castelnuovo.dev',
        ]);

        info('Token: ' . $user->createToken('seeder')->plainTextToken);
    }
}
