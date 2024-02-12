<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AwardRequest;
use App\Http\Requests\Api\V1\AwardsRequest;
use App\Http\Resources\AwardResource;
use App\Models\Award;

class AwardController extends Controller
{
    public function index(AwardsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Award::class);

        return AwardResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(AwardRequest $request, Award $award)
    {
        if ($request->has('load')) {
            $award->load($request->get('load'));
        }

        return AwardResource::make($award);
    }
}
