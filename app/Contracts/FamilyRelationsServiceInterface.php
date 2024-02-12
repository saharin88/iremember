<?php

namespace App\Contracts;

interface FamilyRelationsServiceInterface
{
    public function getDescendantsIds(FamilyRelationsInterface $model): array;
}
