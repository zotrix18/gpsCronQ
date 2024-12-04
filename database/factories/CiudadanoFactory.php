<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ciudadano>
 */
class CiudadanoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ciudads_id' => $this->faker->numberBetween(1, 2),
            'obrasocials_id' => $this->faker->numberBetween(1, 3),
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'dni' => $this->faker->unique()->numberBetween(1000000, 99999999),
            'fechanacimiento' => $this->faker->dateTimeBetween('-90 years', '-18 years')->format('Y-m-d'),
            'sexo' => $this->faker->randomElement(['F', 'M', 'X']),
        ];
    }
}
