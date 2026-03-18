<?php

declare(strict_types=1);

namespace Database\Factories;

use App\ContractSourceType;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'body' => '<p>Contrato de prestação de serviços entre $cliente.nome, CPF $cliente.cpf, residente em $cliente.endereco, $cliente.endereco_numero, $cliente.bairro, $cliente.cidade - $cliente.estado.</p>',
            'source_type' => ContractSourceType::Manual,
        ];
    }

    public function fromUpload(): static
    {
        return $this->state([
            'source_type' => ContractSourceType::Upload,
            'original_file_path' => 'contracts/originals/sample.docx',
        ]);
    }
}
