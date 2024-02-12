<?php

use App\Http\Controllers\Api\V1\AwardController;
use App\Http\Controllers\Api\V1\AwardPeopleController;
use App\Http\Controllers\Api\V1\BattleController;
use App\Http\Controllers\Api\V1\BattlePeopleController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\LocationPeopleController;
use App\Http\Controllers\Api\V1\MemorialController;
use App\Http\Controllers\Api\V1\MilitaryBranchController;
use App\Http\Controllers\Api\V1\MilitaryPositionController;
use App\Http\Controllers\Api\V1\PersonController;
use App\Http\Controllers\Api\V1\RankController;
use App\Http\Controllers\Api\V1\UnitController;
use App\Http\Controllers\Api\V1\UnitPeopleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('awards', [AwardController::class, 'index'])->name('awards.index');
Route::get('award/{award}', [AwardController::class, 'show'])->name('awards.show');
Route::get('award/{award}/people', AwardPeopleController::class)->name('awards.people');

Route::get('battles', [BattleController::class, 'index'])->name('battles.index');
Route::get('battle/{battle}', [BattleController::class, 'show'])->name('battles.show');
Route::get('battle/{battle}/people', BattlePeopleController::class)->name('battles.people');

Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('location/{location}', [LocationController::class, 'show'])->name('locations.show');
Route::get('location/{location}/people/{relation}', LocationPeopleController::class)
    ->whereIn('relation', ['birth', 'death', 'burial', 'wound'])
    ->name('locations.people');

Route::get('memorials', [MemorialController::class, 'index'])->name('memorials.index');
Route::get('memorial/{memorial}', [MemorialController::class, 'show'])->name('memorials.show');

Route::get('military-branches', [MilitaryBranchController::class, 'index'])->name('military-branches.index');
Route::get('military-branch/{militaryBranch}', [MilitaryBranchController::class, 'show'])->name('military-branches.show');

Route::get('military-positions', [MilitaryPositionController::class, 'index'])->name('military-positions.index');
Route::get('military-position/{militaryPosition}', [MilitaryPositionController::class, 'show'])->name('military-positions.show');

Route::get('people', [PersonController::class, 'index'])->name('people.index');
Route::get('person/{person}', [PersonController::class, 'show'])->name('people.show');

Route::get('ranks', [RankController::class, 'index'])->name('ranks.index');
Route::get('rank/{rank}', [RankController::class, 'show'])->name('ranks.show');

Route::get('units', [UnitController::class, 'index'])->name('units.index');
Route::get('unit/{unit}', [UnitController::class, 'show'])->name('units.show');
Route::get('unit/{unit}/people', UnitPeopleController::class)->name('units.people');
