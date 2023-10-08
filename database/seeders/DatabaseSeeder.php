<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name'  => 'Example User',
            'email' => 'admin@example.com',
        ]);
    }
}
