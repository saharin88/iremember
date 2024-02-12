<?php

use App\Models\Location;
use App\Models\Person;

it('should have a route for locations', function () {
    $this->getJson(route('api.locations.index'))->assertOk();
});

it('should have a route for location', function () {
    $location = Location::factory()->create();
    $this->getJson(route('api.locations.show', $location))->assertOk();
});

it('should have a route for people by location', function () {
    $location = Location::factory()->create();
    Person::factory()->create(['birth_location_id' => $location->id]);
    $this->getJson(route('api.locations.people', ['location' => $location, 'relation' => 'birth']))
        ->assertOk()
        ->assertJsonPath('data.0.birth_location_id', $location->id)
        ->assertJsonCount(1, 'data');
});

it('should list locations and people by location with pagination', function () {
    $location = Location::factory(10)->create()->first();
    Person::factory(10)->create(['birth_location_id' => $location->id]);
    $this->getJson(route('api.locations.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
    $this->getJson(route('api.locations.people', ['location' => $location, 'relation' => 'birth', 'limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list locations sorted by name, people_birth_count, people_death_count, people_burial_count, people_wound_count', function () {
    $locations = collect([
        Location::factory()->create(['name' => 'Location 1']),
        Location::factory()->create(['name' => 'Location 2']),
        Location::factory()->create(['name' => 'Location 3']),
    ]);

    $locations->each(function ($location, $index) {
        Person::factory($index + 1)->create(['birth_location_id' => $location->id]);
        Person::factory($index + 1)->create(['death_location_id' => $location->id]);
        Person::factory($index + 1)->create(['burial_location_id' => $location->id]);
        Person::factory($index + 1)->create(['wound_location_id' => $location->id]);
    });

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['name', 'people_birth_count', 'people_death_count', 'people_burial_count', 'people_wound_count'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.locations.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $locations[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $locations[1]->id)
                ->assertJsonPath('data.2.id', $locations[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should get a location(s) with its parent, ancestors, children, descendants, images', function () {

    $location1 = Location::factory()->hasImages(1)->create();
    $location2 = Location::factory()->hasImages(1)->create(['parent_id' => $location1->id]);
    $location3 = Location::factory()->hasImages(1)->create(['parent_id' => $location2->id]);

    $this->getJson(route('api.locations.index', ['load' => ['parent', 'ancestors', 'children', 'descendants', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.0.images.0.id', $location1->images->first()->id)
        ->assertJsonPath('data.0.children.0.id', $location2->id)
        ->assertJsonPath('data.0.descendants.0.descendants.0.id', $location3->id);

    $this->getJson(route('api.locations.show', ['location' => $location3, 'load' => ['parent', 'ancestors', 'children', 'descendants', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.images.0.id', $location3->images->first()->id)
        ->assertJsonPath('data.parent.id', $location2->id)
        ->assertJsonPath('data.ancestors.ancestors.id', $location1->id);

});

it('should filters locations by name, parent_id, lat, lng, koatuu, katottg', function () {
    $location1 = Location::factory()->create(['name' => 'Location 1', 'lat' => 40.0, 'lng' => 20.0, 'koatuu' => '9234567890', 'katottg' => '9234567890123456789']);
    $location2 = Location::factory()->create(['name' => 'Location 2', 'parent_id' => $location1->id, 'lat' => 50.0, 'lng' => 30.0, 'koatuu' => '1234567890', 'katottg' => '1234567890123456789']);
    Location::factory()->create(['name' => 'Location 3', 'parent_id' => $location2->id, 'lat' => 60.0, 'lng' => 40.0, 'koatuu' => '1234567899', 'katottg' => '1234567890123456780']);

    $filters = [
        'name' => 'Location 2',
        'parent_id' => $location1->id,
        'lat' => 50.0,
        'lng' => 30.0,
        'koatuu' => '1234567890',
        'katottg' => '1234567890123456789',
    ];

    foreach ($filters as $key => $value) {
        $this->getJson(route('api.locations.index', ['filter['.$key.']' => $value]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $location2->id)
            ->assertJsonCount(1, 'data');
    }
});
