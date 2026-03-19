<?php

declare(strict_types=1);

namespace Database\Factories;

use App\MaritalStatus;
use App\Models\Contratado;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contratado>
 */
class ContratadoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'rg' => fake()->numerify('##.###.###-#'),
            'birth_date' => fake()->date(),
            'nationality' => 'Brasileiro(a)',
            'marital_status' => fake()->randomElement(MaritalStatus::cases()),
            'profession' => fake()->jobTitle(),
            'address' => fake()->streetName(),
            'address_number' => fake()->buildingNumber(),
            'address_complement' => fake()->optional()->secondaryAddress(),
            'neighborhood' => fake()->word(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'zip_code' => fake()->numerify('#####-###'),
        ];
    }
}
