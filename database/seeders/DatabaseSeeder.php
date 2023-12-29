<?php

namespace Database\Seeders;

use App\Models\Elector;
use App\Models\Nomination;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(count: 10)->create();

        Organisation::factory(count: 10)
            ->has(
                factory: Nomination::factory(count: rand(1, 5))
                    ->has(
                        factory: Elector::factory(count: rand(5, 15)),
                        relationship: 'electors',
                    ),
                relationship: 'nominations',
            )
            ->create();
    }
}
