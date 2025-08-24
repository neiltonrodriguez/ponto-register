<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cpf = $this->faker->numerify('###########');
        
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'cpf' => $cpf,
            'position' => $this->faker->jobTitle(),
            'birth_date' => $this->faker->date(),
            'zip_code' => $this->faker->numerify('########'),
            'address' => $this->faker->streetAddress(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'neighborhood' => $this->faker->word(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'password' => Hash::make(substr($cpf, -6)),
            'role' => $this->faker->randomElement(['admin', 'employee']),
            'admin_id' => null,
        ];
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'admin_id' => null,
        ]);
    }

    /**
     * Indicate that the user should be an employee.
     */
    public function employee(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'employee',
        ]);
    }
}
