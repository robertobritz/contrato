<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Contratante;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContratanteSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        Contratante::factory()
            ->count(10)
            ->for($user)
            ->create();
    }
}
