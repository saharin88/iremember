<?php

use App\Models\Person;
use App\Models\Rank;

it('should have a route for ranks', function () {
    $this->getJson(route('api.ranks.index'))->assertOk();
});

it('should have a route for rank', function () {
    $rank = Rank::factory()->create();
    $this->getJson(route('api.ranks.show', $rank))->assertOk();
});

it('should list ranks with pagination', function () {
    Rank::factory(10)->create();
    $this->getJson(route('api.ranks.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list ranks sorted by name, people_count', function () {

    $ranks = collect();

    for ($i = 1; $i <= 3; $i++) {
        $rank = Rank::factory()->create(['name' => "Rank $i"]);
        Person::factory($i)->create(['rank_id' => $rank->id]);
        $ranks->push($rank);
    }

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['people_count', 'name'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.ranks.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $ranks[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $ranks[1]->id)
                ->assertJsonPath('data.2.id', $ranks[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should get a rank(s) with its militaryBranch', function () {
    $rank = Rank::factory()
        ->forMilitaryBranch()
        ->create();
    $this->getJson(route('api.ranks.index', ['load' => ['militaryBranch']]))
        ->assertOk()
        ->assertJsonPath('data.0.military_branch_id', $rank->militaryBranch->id);
    $this->getJson(route('api.ranks.show', ['rank' => $rank, 'load' => ['militaryBranch']]))
        ->assertOk()
        ->assertJsonPath('data.military_branch_id', $rank->militaryBranch->id);
});

it('should filter ranks by name, military_branch_id', function () {
    Rank::factory()->create(['name' => 'Rank 1']);
    $rank = Rank::factory()->forMilitaryBranch()->create(['name' => 'Rank 2']);
    Rank::factory()->create(['name' => 'Rank 3']);

    $this->getJson(route('api.ranks.index', ['filter' => ['name' => 'Rank 2']]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $rank->id);
    $this->getJson(route('api.ranks.index', ['filter' => ['military_branch_id' => $rank->militaryBranch->id]]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $rank->id);
});
