<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Enums\Citizenship;
use App\Enums\Sex;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Models\Unit;
use App\Services\FamilyRelationsService;
use App\Traits\PersonTrait;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Units')]
class UnitPeopleController extends Controller
{
    use PersonTrait;

    #[Endpoint(title: 'List people by unit', description: 'Get a list of people by unit')]
    #[UrlParam(name: 'unit', type: 'integer', description: 'Unit ID', required: true, example: 1)]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'full_name', enum: ['full_name', 'birth', 'death'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['unit'], enum: self::RELATIONS)]
    #[QueryParam(name: 'filter[full_name]', type: 'string', description: 'Search by full name', required: false)]
    #[QueryParam(name: 'filter[sex]', type: 'string', description: 'Filter Sex', required: false, example: Sex::MALE->value, enum: Sex::class)]
    #[QueryParam(name: 'filter[citizenship]', type: 'string', description: 'Filter Citizenship', required: false, example: Citizenship::UA->value, enum: Citizenship::class)]
    #[QueryParam(name: 'filter[civil]', type: 'string', description: 'Filter Civil', required: false, example: '0', enum: ['0', '1'])]
    #[QueryParam(name: 'filter[birth]', type: 'string', description: 'Filter by birth date', required: false, example: '1990-01-01')]
    #[QueryParam(name: 'filter[death]', type: 'string', description: 'Filter by death date', required: false, example: '2015-01-01')]
    #[QueryParam(name: 'filter[burial]', type: 'string', description: 'Filter by burial date', required: false, example: '2015-02-01')]
    #[QueryParam(name: 'filter[wound]', type: 'string', description: 'Filter by wound date', required: false, example: '2014-12-01')]
    #[QueryParam(name: 'filter[birth_location_id]', type: 'integer', description: 'Filter by birth location id', required: false)]
    #[QueryParam(name: 'filter[death_location_id]', type: 'integer', description: 'Filter by death location id', required: false)]
    #[QueryParam(name: 'filter[burial_location_id]', type: 'integer', description: 'Filter by burial location id', required: false)]
    #[QueryParam(name: 'filter[wound_location_id]', type: 'integer', description: 'Filter by wound location id', required: false)]
    #[QueryParam(name: 'filter[unit_id]', type: 'integer', description: 'Filter by unit id', required: false)]
    #[QueryParam(name: 'filter[rank_id]', type: 'integer', description: 'Filter by rank id', required: false)]
    #[QueryParam(name: 'filter[military_position_id]', type: 'integer', description: 'Filter by military position id', required: false)]
    #[QueryParam(name: 'filter[death_year]', type: 'integer', description: 'Filter by death year', required: false, example: '2014')]
    #[QueryParam(name: 'filter[birth_year]', type: 'integer', description: 'Filter by birth year', required: false, example: '1990')]
    #[QueryParam(name: 'filter[burial_year]', type: 'integer', description: 'Filter by burial year', required: false, example: '2014')]
    #[QueryParam(name: 'filter[wound_year]', type: 'integer', description: 'Filter by wound year', required: false, example: '2014')]
    #[QueryParam(name: 'filter[birth_day]', type: 'integer', description: 'Filter by birth day', required: false, example: '1231')]
    #[QueryParam(name: 'filter[death_day]', type: 'integer', description: 'Filter by death day', required: false, example: '1231')]
    #[QueryParam(name: 'filter[burial_day]', type: 'integer', description: 'Filter by burial day', required: false, example: '1231')]
    #[QueryParam(name: 'filter[wound_day]', type: 'integer', description: 'Filter by wound day', required: false, example: '1231')]
    #[QueryParam(name: 'filter[age]', type: 'integer', description: 'Filter by age', required: false, example: '30')]
    #[ResponseFromApiResource(PersonResource::class, model: Person::class, collection: true, with: self::RELATIONS, paginate: 10)]
    public function __invoke(
        FamilyRelationsService $familyRelationsService,
        QueryBuilderPreparationInterface $queryBuilderPreparation,
        PeopleRequest $request,
        Unit $unit
    ) {
        $filter = $request->get('filter', []);
        $filter['unit_id'] = array_merge(
            [$unit->id],
            $familyRelationsService->getDescendantsIds($unit)
        );
        $request->merge(compact('filter'));
        $people = $queryBuilderPreparation->prepareQueryBuilder($request);

        return PersonResource::collection(
            $people
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }
}
