<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesSeeder::class);

        $user = User::create(attributes: [
            'name' => 'Iliyas M',
            'email' => 'iliyas.m@inodesys.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $user->assignRole(RolesEnum::cases());

        $this->call([
            WorldSeeder::class,
            ElectionPlanSeeder::class,
        ]);
    }
}
