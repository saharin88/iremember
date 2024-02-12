<?php

use App\Models\MilitaryPosition;
use App\Models\Person;

it('should have a route for military positions', function () {
    $this->getJson(route('api.military-positions.index'))->assertOk();
});

it('should have a route for military position', function () {
    $militaryPosition = MilitaryPosition::factory()->create();
    $this->getJson(route('api.military-positions.show', $militaryPosition))->assertOk();
});

it('should list military positions with pagination', function () {
    MilitaryPosition::factory(10)->create();
    $this->getJson(route('api.military-positions.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list military positions sorted by name, people_count', function () {

    $militaryPositions = collect();

    for ($i = 1; $i <= 3; $i++) {
        $militaryPosition = MilitaryPosition::factory()->create(['name' => "MilitaryPosition $i"]);
        Person::factory($i)->create(['military_position_id' => $militaryPosition->id]);
        $militaryPositions->push($militaryPosition);
    }

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['people_count', 'name'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.military-positions.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $militaryPositions[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $militaryPositions[1]->id)
                ->assertJsonPath('data.2.id', $militaryPositions[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should filter military positions by name', function () {
    MilitaryPosition::factory()->create(['name' => 'MilitaryPosition 1']);
    $militaryPosition = MilitaryPosition::factory()->create(['name' => 'MilitaryPosition 2']);
    MilitaryPosition::factory()->create(['name' => 'MilitaryPosition 3']);

    $this->getJson(route('api.military-positions.index', ['filter' => ['name' => 'MilitaryPosition 2']]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $militaryPosition->id);
});
