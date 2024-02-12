<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Resources\PersonResource;
use App\Models\Award;

class AwardPeopleController extends Controller
{
    public function __invoke(PeopleRequest $request, Award $award, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request);
        $queryBuilder->whereHas('awards', fn ($query) => $query->where('award_id', $award->id));

        return PersonResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }
}
