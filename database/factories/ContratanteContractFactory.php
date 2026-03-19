<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Contratante;
use App\Models\ContratanteContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContratanteContract>
 */
class ContratanteContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(),
            'contratante_id' => Contratante::factory(),
            'body' => '<p>Contrato gerado para o contratante.</p>',
            'is_manually_edited' => false,
            'generated_at' => now(),
        ];
    }

    public function manuallyEdited(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_manually_edited' => true,
        ]);
    }
}
