<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\QueryBuilderPreparationInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PeopleRequest;
use App\Http\Requests\Api\V1\PersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;

class PersonController extends Controller
{
    public function index(PeopleRequest $request, QueryBuilderPreparationInterface $queryBuilderPreparation)
    {
        $people = $queryBuilderPreparation->prepareQueryBuilder($request);

        //dd($people->toSql() , $people->getBindings());

        return PersonResource::collection(
            $people
                ->paginate($request->get('limit', 10))
                ->appends($request->query())
        );
    }

    public function show(PersonRequest $request, Person $person)
    {
        if ($request->has('load')) {
            $person->load($request->get('load'));
        }

        return PersonResource::make($person);
    }
}
