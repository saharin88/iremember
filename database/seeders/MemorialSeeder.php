<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Memorial;
use App\Models\Person;
use Illuminate\Database\Seeder;

class MemorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Memorial::factory(100)
            ->hasAttached(Person::inRandomOrder()->limit(10)->get())
            ->for(Location::inRandomOrder()->first())
            ->hasImages(5)
            ->create();
    }
}
