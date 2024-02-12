<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Resources\PersonResource;
use App\Models\Battle;

class BattlePeopleController extends Controller
{
    public function __invoke(PeopleRequest $request, Battle $battle, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request);
        $queryBuilder->whereHas('battles', fn ($query) => $query->where('battle_id', $battle->id));

        return PersonResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }
}
