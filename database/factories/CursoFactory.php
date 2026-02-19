<?php

namespace Database\Factories;

use App\Enums\CursoTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Curso>
 */
class CursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement([CursoTypes::Online->value, CursoTypes::InPerson->value]),
            'maximum_enrollments' => $this->faker->numberBetween(20, 100),
            'registration_deadline' => $this->faker->dateTimeBetween('now', '+3 months'),
        ];
    }
}
