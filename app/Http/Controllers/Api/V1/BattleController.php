<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BattleRequest;
use App\Http\Requests\Api\V1\BattlesRequest;
use App\Http\Resources\BattleResource;
use App\Models\Battle;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Battles')]
class BattleController extends Controller
{
    #[Endpoint(title: 'List battles', description: 'Get a list of battles')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'start', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['location'], enum: ['location', 'images'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by name', required: false)]
    #[QueryParam(name: 'filter[location_id]', type: 'integer', description: 'Filter by location', required: false)]
    #[QueryParam(name: 'filter[start]', type: 'string', description: 'Filter by start date', required: false)]
    #[QueryParam(name: 'filter[end]', type: 'string', description: 'Filter by end date', required: false)]
    #[ResponseFromApiResource(BattleResource::class, model: Battle::class, collection: true, with: ['location', 'images'], paginate: 10)]
    public function index(BattlesRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Battle::class);

        return BattleResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get battle', description: 'Get a single battle')]
    #[UrlParam(name: 'battle', type: 'integer', description: 'Battle ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['location'], enum: ['location', 'images'])]
    #[ResponseFromApiResource(BattleResource::class, model: Battle::class, with: ['location', 'images'])]
    public function show(BattleRequest $request, Battle $battle)
    {
        if ($request->has('load')) {
            $battle->load($request->get('load'));
        }

        return BattleResource::make($battle);
    }
}
