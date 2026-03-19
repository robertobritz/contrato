<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Contratado;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContratadoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        Contratado::factory()
            ->count(10)
            ->for($user)
            ->create();
    }
}
