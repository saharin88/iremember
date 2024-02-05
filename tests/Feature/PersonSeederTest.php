<?php

use App\Enums\Citizenship;
use App\Jobs\SavePhoto;
use App\Models\Person;
use Database\Seeders\PersonSeeder;
use Illuminate\Support\Facades\Bus;
use Revolution\Google\Sheets\Facades\Sheets;

beforeEach(function () {

    // Arrange
    $this->fakePersonData = [
        PersonSeeder::$firstName => fake()->firstName(),
        PersonSeeder::$lastName => fake()->lastName(),
        PersonSeeder::$middleName => fake()->name(),
        PersonSeeder::$callSign => fake()->word(),
        PersonSeeder::$birth => fake()->date(PersonSeeder::$dateFormat),
        PersonSeeder::$death => fake()->date(PersonSeeder::$dateFormat),
        PersonSeeder::$burial => fake()->date(PersonSeeder::$dateFormat),
        PersonSeeder::$wound => fake()->date(PersonSeeder::$dateFormat),
        PersonSeeder::$birthLocation => fake()->city(),
        PersonSeeder::$deathLocation => fake()->city(),
        PersonSeeder::$burialLocation => fake()->city(),
        PersonSeeder::$woundLocation => fake()->city(),
        PersonSeeder::$cemetery => fake()->city(),
        PersonSeeder::$militaryBranch => fake()->words(asText: true),
        PersonSeeder::$unit => fake()->words(asText: true),
        PersonSeeder::$rank => fake()->word(),
        PersonSeeder::$militaryPosition => fake()->words(asText: true),
        PersonSeeder::$deathDetails => fake()->sentences(asText: true),
        PersonSeeder::$obituary => fake()->sentences(asText: true),
        PersonSeeder::$citizenship => fake()->randomElement(array_column(Citizenship::cases(), 'value')),
        PersonSeeder::$civil => (bool) rand(0, 1),
        PersonSeeder::$photo => fake()->imageUrl(),
        PersonSeeder::$link => fake()->url(),
    ];

    Sheets::partialMock()
        ->shouldReceive('spreadsheet', 'sheet')->once()->andReturnSelf()
        ->shouldreceive('get')->once()->andReturn(collect([
            array_keys($this->fakePersonData),
            array_values($this->fakePersonData),
        ]));

    Bus::fake();
    // End Arrange

    // Act
    (new PersonSeeder())->run();
    // End Act

});

it('creates a rank successfully', function () {
    $this->assertDatabaseHas('ranks', ['name' => $this->fakePersonData[PersonSeeder::$rank]]);
});

it('creates a military position successfully', function () {
    $this->assertDatabaseHas('military_positions', ['name' => $this->fakePersonData[PersonSeeder::$militaryPosition]]);
});

it('creates a unit successfully', function () {
    $this->assertDatabaseHas('units', ['name' => $this->fakePersonData[PersonSeeder::$unit]]);
});

it('creates a locations successfully', function () {
    $this->assertDatabaseHas('locations', ['name' => $this->fakePersonData[PersonSeeder::$birthLocation]]);
    $this->assertDatabaseHas('locations', ['name' => $this->fakePersonData[PersonSeeder::$deathLocation]]);
    $this->assertDatabaseHas('locations', ['name' => $this->fakePersonData[PersonSeeder::$burialLocation]]);
    $this->assertDatabaseHas('locations', ['name' => $this->fakePersonData[PersonSeeder::$woundLocation]]);
    $this->assertDatabaseHas('locations', ['name' => $this->fakePersonData[PersonSeeder::$cemetery]]);
});

it('creates a link successfully', function () {
    $this->assertDatabaseHas('links', ['url' => $this->fakePersonData[PersonSeeder::$link]]);
});

test('SavePhoto job is dispatched', function () {
    Bus::assertDispatched(SavePhoto::class);
});

it('creates and fills the person with the correct data', function () {
    $fullName = implode(' ', array_slice($this->fakePersonData, 0, 3));
    $this->assertDatabaseHas('people', ['full_name' => $fullName]);
    $person = Person::whereFullName($fullName)
        ->with(['birthLocation', 'deathLocation', 'burialLocation', 'woundLocation', 'rank', 'militaryPosition', 'unit', 'links'])
        ->first();
    expect($person->first_name)->toBe($this->fakePersonData[PersonSeeder::$firstName])
        ->and($person->last_name)->toBe($this->fakePersonData[PersonSeeder::$lastName])
        ->and($person->middle_name)->toBe($this->fakePersonData[PersonSeeder::$middleName])
        ->and($person->call_sign)->toBe($this->fakePersonData[PersonSeeder::$callSign])
        ->and($person->birth->format(PersonSeeder::$dateFormat))->toBe($this->fakePersonData[PersonSeeder::$birth])
        ->and($person->death->format(PersonSeeder::$dateFormat))->toBe($this->fakePersonData[PersonSeeder::$death])
        ->and($person->burial->format(PersonSeeder::$dateFormat))->toBe($this->fakePersonData[PersonSeeder::$burial])
        ->and($person->wound->format(PersonSeeder::$dateFormat))->toBe($this->fakePersonData[PersonSeeder::$wound])
        ->and($person->birthLocation->name)->toBe($this->fakePersonData[PersonSeeder::$birthLocation])
        ->and($person->deathLocation->name)->toBe($this->fakePersonData[PersonSeeder::$deathLocation])
        ->and($person->burialLocation->name)->toBe($this->fakePersonData[PersonSeeder::$cemetery])
        ->and($person->woundLocation->name)->toBe($this->fakePersonData[PersonSeeder::$woundLocation])
        ->and($person->rank->name)->toBe($this->fakePersonData[PersonSeeder::$rank])
        ->and($person->militaryPosition->name)->toBe($this->fakePersonData[PersonSeeder::$militaryPosition])
        ->and($person->unit->name)->toBe($this->fakePersonData[PersonSeeder::$unit])
        ->and($person->death_details)->toBe($this->fakePersonData[PersonSeeder::$deathDetails])
        ->and($person->obituary)->toBe($this->fakePersonData[PersonSeeder::$obituary])
        ->and($person->citizenship->value)->toBe($this->fakePersonData[PersonSeeder::$citizenship])
        ->and($person->civil)->toBe($this->fakePersonData[PersonSeeder::$civil])
        ->and($person->links->first()->url)->toBe($this->fakePersonData[PersonSeeder::$link]);
});
