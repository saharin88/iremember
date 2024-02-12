<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Resources\PersonResource;
use App\Models\Location;
use App\Services\FamilyRelationsService;

class LocationPeopleController extends Controller
{
    public function __invoke(
        QueryBuilderPreparationInterface $queryBuilderPreparation,
        FamilyRelationsService $familyRelationsService,
        PeopleRequest $request,
        Location $location,
        string $relation
    ) {
        $filter = $request->get('filter', []);
        $filter[strtolower($relation).'_location_id'] = array_merge(
            [$location->id],
            $familyRelationsService->getDescendantsIds($location)
        );
        $request->merge(compact('filter'));
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request);

        return PersonResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }
}
