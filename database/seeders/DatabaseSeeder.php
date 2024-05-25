<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Nnjeim\World\Actions\SeedAction;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->create(attributes: [
                'name' => 'Iliyas M',
                'email' => 'iliyas.m@inodesys.com',
                'password' => 'password',
            ]);

        $this->call([
            WorldSeeder::class,
            ElectionPlanSeeder::class,
        ]);
    }
}
