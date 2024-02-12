<?php

namespace App\Traits;

trait PersonTrait
{
    const array RELATIONS = [
        'unit',
        'unit.ancestors',
        'rank',
        'militaryPosition',
        'awards',
        'battles',
        'memorials',
        'units',
        'links',
        'photos',
        'birthLocation',
        'birthLocation.ancestors',
        'deathLocation',
        'deathLocation.ancestors',
        'burialLocation',
        'burialLocation.ancestors',
        'woundLocation',
        'woundLocation.ancestors',
    ];
}
