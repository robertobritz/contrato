<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientContract;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientContract>
 */
class ClientContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(),
            'client_id' => Client::factory(),
            'body' => '<p>Contrato gerado para o cliente.</p>',
            'is_manually_edited' => false,
            'generated_at' => now(),
        ];
    }

    public function manuallyEdited(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_manually_edited' => true,
        ]);
    }
}
