<?php

namespace Database\Seeders;

use App\Models\Battle;
use App\Models\Location;
use App\Models\Person;
use Illuminate\Database\Seeder;

class BattleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Battle::factory(100)
            ->hasAttached(Person::inRandomOrder()->limit(100)->get())
            ->for(Location::inRandomOrder()->first())
            ->hasImages(10)
            ->create();

    }
}
