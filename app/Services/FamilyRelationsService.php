<?php

namespace App\Services;

use App\Contracts\FamilyRelationsInterface;
use App\Contracts\FamilyRelationsServiceInterface;

class FamilyRelationsService implements FamilyRelationsServiceInterface
{
    public function getDescendantsIds(FamilyRelationsInterface $model): array
    {
        $keyName = $model->getKeyName();
        $model->load('descendants');

        return $model->descendants->pipe(function ($collection) use ($keyName) {
            $array = $collection->toArray();
            $ids = [];
            array_walk_recursive($array, function ($value, $key) use (&$ids, $keyName) {
                if ($key === $keyName) {
                    $ids[] = $value;
                }
            });

            return array_unique($ids);
        });
    }
}
