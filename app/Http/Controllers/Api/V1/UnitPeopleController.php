<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Resources\PersonResource;
use App\Models\Unit;
use App\Services\FamilyRelationsService;

class UnitPeopleController extends Controller
{
    /**
     * Handle the incoming request.
     */
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
