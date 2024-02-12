<?php

use App\Models\Battle;
use App\Models\Location;
use App\Models\Person;

it('should have a route for battles', function () {
    $this->getJson(route('api.battles.index'))->assertOk();
});

it('should have a route for battle', function () {
    $battle = Battle::factory()->create();
    $this->getJson(route('api.battles.show', $battle))->assertOk();
});

it('should have a route for people by battle', function () {
    $person = Person::factory(2)->create()->first();
    $battle = Battle::factory(1)
        ->hasAttached($person)
        ->create()
        ->first();
    $this->getJson(route('api.battles.people', $battle))
        ->assertOk()
        ->assertJsonPath('data.0.id', $person->id)
        ->assertJsonCount(1, 'data');
});

it('should list battles and people by battle with pagination', function () {
    Battle::factory(10)->create();
    $battle = Battle::factory()
        ->hasAttached(Person::factory(10))
        ->create();
    $this->getJson(route('api.battles.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
    $this->getJson(route('api.battles.people', ['battle' => $battle, 'limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list battles sorted by name, start, people_count', function () {
    $battles = collect([
        Battle::factory()->create(['name' => 'Battle 1', 'start' => '2020-01-01']),
        Battle::factory()->create(['name' => 'Battle 2', 'start' => '2020-01-02']),
        Battle::factory()->create(['name' => 'Battle 3', 'start' => '2020-01-03']),
    ]);

    $battles->each(function ($battle, $index) {
        $battle->people()->attach(Person::factory($index + 1)->create());
    });

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['people_count', 'name', 'start'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.battles.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $battles[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $battles[1]->id)
                ->assertJsonPath('data.2.id', $battles[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should get a battle(s) with its location, images', function () {
    $battle = Battle::factory()->for(Location::factory())->hasImages(1)->create();
    $this->getJson(route('api.battles.index', ['load' => ['location', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.0.images.0.id', $battle->images->first()->id)
        ->assertJsonPath('data.0.location.id', $battle->location->id);
    $this->getJson(route('api.battles.show', ['battle' => $battle, 'load' => ['location', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.images.0.id', $battle->images->first()->id)
        ->assertJsonPath('data.location.id', $battle->location->id);
});

it('should filters battles by name, location_id, start, end', function () {
    $location1 = Location::factory()->create();
    $location2 = Location::factory()->create();
    $battle1 = Battle::factory()->create(['name' => 'Battle 1', 'location_id' => $location1->id, 'start' => '2020-01-01', 'end' => '2020-01-02']);
    $battle2 = Battle::factory()->create(['name' => 'Battle 2', 'location_id' => $location2->id, 'start' => '2020-01-03', 'end' => '2020-01-04']);

    $filters = [
        'name' => 'Battle 1',
        'location_id' => $location1->id,
        'start' => '2020-01-01',
        'end' => '2020-01-02',
    ];

    foreach ($filters as $key => $value) {
        $this->getJson(route('api.battles.index', ['filter['.$key.']' => $value]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $battle1->id)
            ->assertJsonMissing(['id' => $battle2->id]);
    }
});
