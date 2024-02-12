<?php

use App\Models\Location;
use App\Models\Memorial;
use App\Models\Person;

it('should have a route for memorials', function () {
    $this->getJson(route('api.memorials.index'))->assertOk();
});

it('should have a route for memorial', function () {
    $memorial = Memorial::factory()->create();
    $this->getJson(route('api.memorials.show', $memorial))->assertOk();
});

it('should list memorials with pagination', function () {
    Memorial::factory(10)->create();
    $this->getJson(route('api.memorials.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list memorials sorted by name, date, people_count', function () {
    $memorials = collect([
        Memorial::factory()->create(['name' => 'Memorial 1', 'date' => '2020-01-01']),
        Memorial::factory()->create(['name' => 'Memorial 2', 'date' => '2020-01-02']),
        Memorial::factory()->create(['name' => 'Memorial 3', 'date' => '2020-01-03']),
    ]);

    $memorials->each(function ($memorial, $index) {
        $memorial->people()->attach(Person::factory($index + 1)->create());
    });

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['people_count', 'name', 'date'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.memorials.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $memorials[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $memorials[1]->id)
                ->assertJsonPath('data.2.id', $memorials[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should get a memorial(s) with its location, people, images', function () {
    $location = Location::factory()->create();
    $memorial = Memorial::factory()
        ->for($location)
        ->hasPeople()
        ->hasImages()
        ->create();
    $this->getJson(route('api.memorials.index', ['load' => ['location', 'people', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.0.location.id', $location->id)
        ->assertJsonPath('data.0.people.0.id', $memorial->people->first()->id)
        ->assertJsonPath('data.0.images.0.id', $memorial->images->first()->id);
    $this->getJson(route('api.memorials.show', ['memorial' => $memorial, 'load' => ['location', 'people', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.location.id', $location->id)
        ->assertJsonPath('data.people.0.id', $memorial->people->first()->id)
        ->assertJsonPath('data.images.0.id', $memorial->images->first()->id);
});

it('should filter memorials by name, location_id, date', function () {
    $location1 = Location::factory()->create();
    $location2 = Location::factory()->create();
    $memorial1 = Memorial::factory()->create(['name' => 'Memorial 1', 'location_id' => $location1->id, 'date' => '2020-01-01']);
    $memorial2 = Memorial::factory()->create(['name' => 'Memorial 2', 'location_id' => $location2->id, 'date' => '2020-01-02']);

    $filters = [
        'name' => 'Memorial 2',
        'location_id' => $location2->id,
        'date' => '2020-01-02',
    ];

    foreach ($filters as $key => $value) {
        $this->getJson(route('api.memorials.index', ['filter['.$key.']' => $value]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $memorial2->id)
            ->assertJsonMissing(['id' => $memorial1->id]);
    }
});
