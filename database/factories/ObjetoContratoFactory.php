<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contratado;
use App\Models\Contratante;
use App\Models\ObjetoContrato;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObjetoContrato>
 */
class ObjetoContratoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contratante_id' => Contratante::factory(),
            'contratado_id' => Contratado::factory(),
            'tipo' => fake()->randomElement(['servico', 'produto']),
            'descricao' => fake()->sentence(),
            'quantidade' => fake()->randomFloat(2, 1, 100),
            'unidade' => fake()->randomElement(['un', 'hr', 'kg', 'm²', 'mês']),
            'valor' => fake()->randomFloat(2, 50, 10000),
        ];
    }
}
