<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'koatuu' => $this->faker->numerify('##########'),
            'katottg' => $this->faker->numerify('UA#################'),
        ];
    }
}
