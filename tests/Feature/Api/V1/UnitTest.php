<?php

use App\Models\Person;
use App\Models\Unit;

it('should have a route for units', function () {
    $this->getJson(route('api.units.index'))->assertOk();
});

it('should have a route for unit', function () {
    $unit = Unit::factory()->create();
    $this->getJson(route('api.units.show', $unit))->assertOk();
});

it('should have a route for people by unit', function () {
    $unit = Unit::factory()->create();
    $person = Person::factory()->create(['unit_id' => $unit->id]);
    $this->getJson(route('api.units.people', $unit))
        ->assertOk()
        ->assertJsonPath('data.0.id', $person->id)
        ->assertJsonCount(1, 'data');
});

it('should list units and people by unit with pagination', function () {
    $unit = Unit::factory(10)->create()->first();
    Person::factory(10)->create(['unit_id' => $unit->id]);
    $this->getJson(route('api.units.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
    $this->getJson(route('api.units.people', ['unit' => $unit, 'limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list units sorted by name, people_count', function () {

    $units = collect();

    for ($i = 1; $i <= 3; $i++) {
        $unit = Unit::factory()->create(['name' => "Unit $i"]);
        Person::factory($i)->create(['unit_id' => $unit->id]);
        $units->push($unit);
    }

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['name', 'people_count'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.units.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $units[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $units[1]->id)
                ->assertJsonPath('data.2.id', $units[$order === 'asc' ? 2 : 0]->id);

        }
    }
});

it('should get a unit(s) with its parent, ancestors, children, descendants, militaryBranch, images', function () {
    $unit1 = Unit::factory()->hasImages(1)->forMilitaryBranch()->create();
    $unit2 = Unit::factory()->hasImages(1)->forMilitaryBranch()->create(['parent_id' => $unit1->id]);
    $unit3 = Unit::factory()->hasImages(1)->forMilitaryBranch()->create(['parent_id' => $unit2->id]);
    $this->getJson(route('api.units.index', ['load' => ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.0.militaryBranch.id', $unit1->militaryBranch->id)
        ->assertJsonPath('data.0.images.0.id', $unit1->images->first()->id)
        ->assertJsonPath('data.0.children.0.id', $unit1->children->first()->id)
        ->assertJsonPath('data.0.descendants.0.descendants.0.id', $unit3->id);

    $this->getJson(route('api.units.show', ['unit' => $unit3, 'load' => ['parent', 'ancestors', 'children', 'descendants', 'militaryBranch', 'images']]))
        ->assertOk()
        ->assertJsonPath('data.parent.id', $unit2->id)
        ->assertJsonPath('data.ancestors.ancestors.id', $unit1->id)
        ->assertJsonPath('data.militaryBranch.id', $unit3->militaryBranch->id)
        ->assertJsonPath('data.images.0.id', $unit3->images->first()->id);
});

it('should filter units by name, parent_id, military_branch_id', function () {
    $unit1 = Unit::factory()->create(['name' => 'Unit 1']);
    $unit2 = Unit::factory()->forMilitaryBranch()->create(['name' => 'Unit 2', 'parent_id' => $unit1->id]);
    $this->getJson(route('api.units.index', ['filter' => ['name' => 'Unit 2']]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $unit2->id);
    $this->getJson(route('api.units.index', ['filter' => ['military_branch_id' => $unit2->militaryBranch->id]]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $unit2->id);
    $this->getJson(route('api.units.index', ['filter' => ['parent_id' => $unit1->id]]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $unit2->id);
});
