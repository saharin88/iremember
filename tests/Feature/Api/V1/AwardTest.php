<?php

use App\Models\Award;
use App\Models\Person;

it('should have a route for awards', function () {
    $this->getJson(route('api.awards.index'))->assertOk();
});

it('should have a route for award', function () {
    $award = Award::factory()->create();
    $this->getJson(route('api.awards.show', $award))->assertOk();
});

it('should have a route for people by award', function () {
    $person = Person::factory(2)->create()->first();
    $award = Award::factory(1)
        ->hasAttached($person)
        ->create()
        ->first();
    $this->getJson(route('api.awards.people', $award))
        ->assertOk()
        ->assertJsonPath('data.0.id', $person->id)
        ->assertJsonCount(1, 'data');
});

it('should list awards sorted by name', function () {
    $awards = collect([
        Award::factory()->create(['name' => 'Award 1']),
        Award::factory()->create(['name' => 'Award 2']),
        Award::factory()->create(['name' => 'Award 3']),
    ]);

    $sortOrders = ['asc', 'desc'];

    foreach ($sortOrders as $order) {
        $this->getJson(route('api.awards.index', ['sort' => 'name', 'order' => $order]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $awards[$order === 'asc' ? 0 : 2]->id)
            ->assertJsonPath('data.1.id', $awards[1]->id)
            ->assertJsonPath('data.2.id', $awards[$order === 'asc' ? 2 : 0]->id);
    }
});

it('should list awards and people by award with pagination', function () {
    Award::factory(10)->create();
    $award = Award::factory()
        ->hasAttached(Person::factory(10))
        ->create();
    $this->getJson(route('api.awards.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
    $this->getJson(route('api.awards.people', ['award' => $award, 'limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should get a award(s) with its images', function () {
    $award = Award::factory()
        ->hasImages(1)
        ->create();
    $this->getJson(route('api.awards.index', ['award' => $award, 'load' => ['images']]))
        ->assertOk()
        ->assertJsonPath('data.0.images.0.id', $award->images->first()->id);
    $this->getJson(route('api.awards.show', ['award' => $award, 'load' => ['images']]))
        ->assertOk()
        ->assertJsonPath('data.images.0.id', $award->images->first()->id);
});

it('should filter awards by name', function () {
    $award1 = Award::factory()->create(['name' => 'Award 1']);
    $award2 = Award::factory()->create(['name' => 'Award 2']);
    $this->getJson(route('api.awards.index', ['filter[name]' => 'Award 1']))
        ->assertOk()
        ->assertJsonPath('data.0.id', $award1->id)
        ->assertJsonMissing(['id' => $award2->id]);
});
