<?php

use App\Models\MilitaryBranch;

it('should have a route for military branches', function () {
    $this->getJson(route('api.military-branches.index'))->assertOk();
});

it('should have a route for military branch', function () {
    $militaryBranch = MilitaryBranch::factory()->create();
    $this->getJson(route('api.military-branches.show', $militaryBranch))->assertOk();
});

it('should list military branches with pagination', function () {
    MilitaryBranch::factory(10)->create();
    $this->getJson(route('api.military-branches.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list military branches sorted by name', function () {
    $militaryBranches = collect([
        MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 1']),
        MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 2']),
        MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 3']),
    ]);

    $sortOrders = ['asc', 'desc'];

    foreach ($sortOrders as $order) {
        $this->getJson(route('api.military-branches.index', ['sort' => 'name', 'order' => $order]))
            ->assertOk()
            ->assertJsonPath('data.0.id', $militaryBranches[$order === 'asc' ? 0 : 2]->id)
            ->assertJsonPath('data.1.id', $militaryBranches[1]->id)
            ->assertJsonPath('data.2.id', $militaryBranches[$order === 'asc' ? 2 : 0]->id);
    }
});

it('should get a military branch(s) with its parent, ancestors, children, descendants, ranks, units', function () {
    $militaryBranch1 = MilitaryBranch::factory()->hasRanks(1)->hasUnits(1)->create();
    $militaryBranch2 = MilitaryBranch::factory()->hasRanks(1)->hasUnits(1)->create(['parent_id' => $militaryBranch1->id]);
    $militaryBranch3 = MilitaryBranch::factory()->hasRanks(1)->hasUnits(1)->create(['parent_id' => $militaryBranch2->id]);

    $this->getJson(route('api.military-branches.index', ['load' => ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units']]))
        ->assertOk()
        ->assertJsonPath('data.0.children.0.id', $militaryBranch2->id)
        ->assertJsonPath('data.0.descendants.0.descendants.0.id', $militaryBranch3->id)
        ->assertJsonPath('data.0.ranks.0.id', $militaryBranch1->ranks->first()->id)
        ->assertJsonPath('data.0.units.0.id', $militaryBranch1->units->first()->id);

    $this->getJson(route('api.military-branches.show', ['militaryBranch' => $militaryBranch3, 'load' => ['parent', 'ancestors', 'children', 'descendants', 'ranks', 'units']]))
        ->assertOk()
        ->assertJsonPath('data.parent.id', $militaryBranch2->id)
        ->assertJsonPath('data.ancestors.ancestors.id', $militaryBranch1->id)
        ->assertJsonPath('data.ranks.0.id', $militaryBranch3->ranks->first()->id)
        ->assertJsonPath('data.units.0.id', $militaryBranch3->units->first()->id);
});

it('should filters military branches by name, parent_id', function () {
    MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 1']);
    $militaryBranch2 = MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 2']);
    $militaryBranch3 = MilitaryBranch::factory()->create(['name' => 'MilitaryBranch 3', 'parent_id' => $militaryBranch2->id]);

    $this->getJson(route('api.military-branches.index', ['filter' => ['name' => 'MilitaryBranch 2']]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $militaryBranch2->id);

    $this->getJson(route('api.military-branches.index', ['filter' => ['parent_id' => $militaryBranch2->id]]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $militaryBranch3->id);
});
