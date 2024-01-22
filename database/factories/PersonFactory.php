<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'call_sign' => $this->faker->name,
            'birth' => $this->faker->date(),
            'death' => $this->faker->date(),
            'burial' => $this->faker->date(),
            'wound' => $this->faker->date(),
            'death_details' => $this->faker->text,
            'photo' => $this->faker->imageUrl(),
        ];
    }
}
