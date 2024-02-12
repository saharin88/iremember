<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MemorialRequest;
use App\Http\Requests\Api\V1\MemorialsRequest;
use App\Http\Resources\MemorialResource;
use App\Models\Memorial;

class MemorialController extends Controller
{
    public function index(MemorialsRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $queryBuilder = $queryBuilderPreparation->prepareQueryBuilder($request, Memorial::class);

        return MemorialResource::collection(
            $queryBuilder
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(MemorialRequest $request, Memorial $memorial)
    {
        if ($request->has('load')) {
            $memorial->load($request->get('load'));
        }

        return MemorialResource::make($memorial);
    }
}
