<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Person;
use Illuminate\Database\Seeder;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Award::factory(100)
            ->hasAttached(
                Person::inRandomOrder()->limit(100)->get(),
                [
                    'date' => fake()->date(),
                    'additional_info' => fake()->text(),
                ]
            )
            ->hasImages(10)
            ->create();
    }
}
