<?php

namespace App\Traits;

trait Sluggable
{
    use \Cviebrock\EloquentSluggable\Sluggable;

    public function resolveRouteBinding($value, $field = null)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $field = 'slug';
        }

        return parent::resolveRouteBinding($value, $field);
    }
}
