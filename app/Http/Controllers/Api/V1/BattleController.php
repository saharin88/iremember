<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BattleRequest;
use App\Http\Requests\Api\V1\BattlesRequest;
use App\Http\Resources\BattleResource;
use App\Models\Battle;

class BattleController extends Controller
{
    public function index(BattlesRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Battle::class);

        return BattleResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(BattleRequest $request, Battle $battle)
    {
        if ($request->has('load')) {
            $battle->load($request->get('load'));
        }

        return BattleResource::make($battle);
    }
}
