<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RankRequest;
use App\Http\Requests\Api\V1\RanksRequest;
use App\Http\Resources\RankResource;
use App\Models\Rank;

class RankController extends Controller
{
    public function index(RanksRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Rank::class);

        return RankResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(RankRequest $request, Rank $rank)
    {
        if ($request->has('load')) {
            $rank->load($request->get('load'));
        }

        return RankResource::make($rank);
    }
}
