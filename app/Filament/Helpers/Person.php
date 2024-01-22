<?php

namespace App\Filament\Helpers;

use Filament\Tables;

final class Person
{
    public static function getLocationFilters(array|string $relations): array
    {

        $filters = [];

        if (is_array($relations)) {
            foreach ($relations as $relation) {
                $filters = array_merge($filters, self::getLocationFilters($relation));
            }
        } else {
            $filters[] = Tables\Filters\SelectFilter::make($relations.'_location_id')
                ->relationship($relations.'Location', 'name')
                ->label(__(ucfirst($relations).' location'))
                ->searchable()
                ->columnSpan(2);
        }

        return $filters;

    }
}
