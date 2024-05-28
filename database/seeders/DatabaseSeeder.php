<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(attributes: [
            'name' => 'Iliyas M',
            'email' => 'iliyas.m@inodesys.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $this->call([
            WorldSeeder::class,
            ElectionPlanSeeder::class,
        ]);
    }
}
