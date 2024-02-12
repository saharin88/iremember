<?php

use App\Models\Award;
use App\Models\Battle;
use App\Models\Image;
use App\Models\Link;
use App\Models\Memorial;
use App\Models\Person;
use App\Models\Unit;
use Illuminate\Support\Carbon;

it('should have a route for people', function () {
    $this->getJson(route('api.people.index'))->assertOk();
});

it('should have a route for person', function () {
    $person = Person::factory()->create();
    $this->getJson(route('api.people.show', $person))->assertOk();
});

it('should list people with pagination', function () {
    Person::factory(10)->create();
    $this->getJson(route('api.people.index', ['limit' => 5, 'page' => 1]))
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('should list people sorted by full_name, birth, death', function () {

    $people = collect();

    for ($i = 1; $i <= 3; $i++) {
        $person = Person::factory()->create([
            'first_name' => "Person $i", 'last_name' => "Person $i", 'middle_name' => "Person $i",
            'birth' => "2020-01-0$i", 'death' => "2020-01-0$i",
        ]);
        $people->push($person);
    }

    $sortOrders = ['asc', 'desc'];
    $sortFields = ['full_name', 'birth', 'death'];

    foreach ($sortFields as $field) {
        foreach ($sortOrders as $order) {
            $this->getJson(route('api.people.index', ['sort' => $field, 'order' => $order]))
                ->assertOk()
                ->assertJsonPath('data.0.id', $people[$order === 'asc' ? 0 : 2]->id)
                ->assertJsonPath('data.1.id', $people[1]->id)
                ->assertJsonPath('data.2.id', $people[$order === 'asc' ? 2 : 0]->id);
        }
    }
});

it('should get a person and people with its unit, rank, militaryPosition, awards, battles, memorials, units, links, photos, birthLocation, deathLocation, burialLocation, woundLocation',
    function () {

        $person = Person::factory()
            ->forUnit()
            ->forRank()
            ->forMilitaryPosition()
            ->forBirthLocation()
            ->forDeathLocation()
            ->forBurialLocation()
            ->forWoundLocation()
            ->hasAwards(Award::factory(2))
            ->hasBattles(Battle::factory(2))
            ->hasMemorials(Memorial::factory(2))
            ->hasUnits(Unit::factory(2))
            ->hasLinks(Link::factory(2))
            ->hasPhotos(Image::factory(2))
            ->create();

        $requests = [
            'data' => route('api.people.show', ['person' => $person, 'load' => self::RELATIONS]),
            'data.0' => route('api.people.index', ['load' => self::RELATIONS]),
        ];

        foreach ($requests as $data => $route) {
            $this->getJson($route)
                ->assertOk()
                ->assertJsonPath("$data.unit.id", $person->unit->id)
                ->assertJsonPath("$data.rank.id", $person->rank->id)
                ->assertJsonPath("$data.militaryPosition.id", $person->militaryPosition->id)
                ->assertJsonPath("$data.birthLocation.id", $person->birthLocation->id)
                ->assertJsonPath("$data.deathLocation.id", $person->deathLocation->id)
                ->assertJsonPath("$data.burialLocation.id", $person->burialLocation->id)
                ->assertJsonPath("$data.woundLocation.id", $person->woundLocation->id)
                ->assertJsonPath("$data.awards.0.id", $person->awards[0]->id)
                ->assertJsonPath("$data.battles.0.id", $person->battles[0]->id)
                ->assertJsonPath("$data.memorials.0.id", $person->memorials[0]->id)
                ->assertJsonPath("$data.units.0.id", $person->units[0]->id)
                ->assertJsonPath("$data.links.0.id", $person->links[0]->id)
                ->assertJsonPath("$data.photos.0.id", $person->photos[0]->id);
        }
    });

it('should filter people by full_name, sex, citizenship, civil, birth, death, burial, wound, birth_location_id, death_location_id, burial_location_id, wound_location_id, unit_id, rank_id, military_position_id, death_year, birth_year, burial_year, wound_year, death_day, birth_day, burial_day, wound_day, years_old',
    function () {

        $persons = collect();

        for ($k = 0; $k < 10; $k++) {
            for ($i = 0; $i < rand(5, 9); $i++) {
                $person = Person::factory()
                    ->forUnit()
                    ->forRank()
                    ->forMilitaryPosition()
                    ->forBirthLocation()
                    ->forDeathLocation()
                    ->forBurialLocation()
                    ->forWoundLocation()
                    ->create([
                        'first_name' => "Person $i",
                        'last_name' => "Person $i",
                        'middle_name' => "Person $i",
                        'sex' => $i % 2 === 0 ? 0 : 1,
                        'citizenship' => $i % 2 === 0 ? 'UA' : 'OTHER',
                        'civil' => $i % 2 === 0 ? 1 : 0,
                        'birth' => Carbon::createFromFormat('Y-m-d', rand(1960, 2000)."-03-0$i"),
                        'death' => Carbon::createFromFormat('Y-m-d', rand(2014, (int) date('Y'))."-02-0$i"),
                        'burial' => Carbon::createFromFormat('Y-m-d', rand(2014, (int) date('Y'))."-09-0$i"),
                        'wound' => Carbon::createFromFormat('Y-m-d', rand(2014, (int) date('Y'))."-06-0$i"),
                    ]);
                $persons->push($person->fresh());
            }
        }

        $person = $persons->random();
        $count = $persons->count();

        $otherPersons = $persons->filter(fn($p) => $p->id !== $person->id);

        foreach (['birth', 'death', 'burial', 'wound'] as $field) {
            $otherPersons->random()->update([$field => null]);
        }

        $counts = [
            'full_name' => $persons->where('full_name', 'LIKE', $person->full_name)->count(),
            'sex' => $persons->where('sex', $person->sex)->count(),
            'citizenship' => $persons->where('citizenship', $person->citizenship)->count(),
            'civil' => $persons->where('civil', $person->civil)->count(),
            'birth' => $persons->where('birth', $person->birth)->count(),
            'death' => $persons->where('death', $person->death)->count(),
            'burial' => $persons->where('burial', $person->burial)->count(),
            'wound' => $persons->where('wound', $person->wound)->count(),
            'unit_id' => $persons->where('unit_id', $person->unit_id)->count(),
            'rank_id' => $persons->where('rank_id', $person->rank_id)->count(),
            'military_position_id' => $persons->where('military_position_id', $person->military_position_id)->count(),
            'birth_location_id' => $persons->where('birth_location_id', $person->birth_location_id)->count(),
            'death_location_id' => $persons->where('death_location_id', $person->death_location_id)->count(),
            'burial_location_id' => $persons->where('burial_location_id', $person->burial_location_id)->count(),
            'wound_location_id' => $persons->where('wound_location_id', $person->wound_location_id)->count(),
            'death_year' => $persons->filter(fn($p) => $p->death?->year === $person->death->year)->count(),
            'birth_year' => $persons->filter(fn($p) => $p->birth?->year === $person->birth->year)->count(),
            'burial_year' => $persons->filter(fn($p) => $p->burial?->year === $person->burial->year)->count(),
            'wound_year' => $persons->filter(fn($p) => $p->wound?->year === $person->wound->year)->count(),
            'death_day' => $persons->filter(fn($p) => $p->death?->format('md') === $person->death->format('md'))->count(),
            'birth_day' => $persons->filter(fn($p) => $p->birth?->format('md') === $person->birth->format('md'))->count(),
            'burial_day' => $persons->filter(fn($p) => $p->burial?->format('md') === $person->burial->format('md'))->count(),
            'wound_day' => $persons->filter(fn($p) => $p->wound?->format('md') === $person->wound->format('md'))->count(),
            'age' => $persons->filter(fn($p) => $p->death?->diff($p->birth)->format('%y') === $person->death->diff($person->birth)->format('%y'))->count(),
        ];

        $filters = [
            'full_name' => $person->full_name,
            'sex' => $person->sex->value,
            'citizenship' => $person->citizenship->value,
            'civil' => $person->civil,
            'birth' => $person->birth->format('Y-m-d'),
            'death' => $person->death->format('Y-m-d'),
            'burial' => $person->burial->format('Y-m-d'),
            'wound' => $person->wound->format('Y-m-d'),
            'birth_location_id' => $person->birth_location_id,
            'death_location_id' => $person->death_location_id,
            'burial_location_id' => $person->burial_location_id,
            'wound_location_id' => $person->wound_location_id,
            'unit_id' => $person->unit_id,
            'rank_id' => $person->rank_id,
            'military_position_id' => $person->military_position_id,
            'death_year' => $person->death->year,
            'birth_year' => $person->birth->year,
            'burial_year' => $person->burial->year,
            'wound_year' => $person->wound->year,
            'death_day' => $person->death->format('md'),
            'birth_day' => $person->birth->format('md'),
            'burial_day' => $person->burial->format('md'),
            'wound_day' => $person->wound->format('md'),
            'age' => $person->death->diff($person->birth)->format('%y'),
        ];

        foreach ($filters as $filter => $value) {
            $this->getJson(route('api.people.index', [
                'filter' => [$filter => $value],
                'limit' => $count,
            ]))->assertOk()
                ->assertJsonCount($counts[$filter] ?? 1, 'data')
                ->assertJsonFragment(['id' => $person->id]);
        }

    });
