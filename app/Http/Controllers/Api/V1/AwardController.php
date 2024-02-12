<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AwardRequest;
use App\Http\Requests\Api\V1\AwardsRequest;
use App\Http\Resources\AwardResource;
use App\Models\Award;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group(name: 'Awards')]
class AwardController extends Controller
{
    #[Endpoint(title: 'List awards', description: 'Get a list of awards')]
    #[QueryParam(name: 'limit', type: 'integer', description: 'Number of items to return per page', required: false, example: 100)]
    #[QueryParam(name: 'page', type: 'integer', description: 'Page number', required: false, example: 1)]
    #[QueryParam(name: 'sort', type: 'string', description: 'Sort by column', required: false, example: 'name', enum: ['name', 'people_count'])]
    #[QueryParam(name: 'order', type: 'string', description: 'Sort order', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['images'], enum: ['images'])]
    #[QueryParam(name: 'filter[name]', type: 'string', description: 'Filter by award name', required: false)]
    #[ResponseFromApiResource(AwardResource::class, model: Award::class, collection: true, with: ['images'], paginate: 10)]
    public function index(AwardsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Award::class);

        return AwardResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    #[Endpoint(title: 'Get award', description: 'Get a single award')]
    #[UrlParam(name: 'award', type: 'integer', description: 'Award ID', required: true, example: 1)]
    #[QueryParam(name: 'load', type: 'array', description: 'Eager load relations', required: false, example: ['images'], enum: ['images'])]
    #[ResponseFromApiResource(AwardResource::class, model: Award::class, with: ['images'])]
    public function show(AwardRequest $request, Award $award)
    {
        if ($request->has('load')) {
            $award->load($request->get('load'));
        }

        return AwardResource::make($award);
    }
}
