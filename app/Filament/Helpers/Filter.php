<?php

namespace App\Filament\Helpers;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

final class Filter
{
    public static function date(string $column): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make($column)
            ->form([
                Forms\Components\Fieldset::make(__('Date of '.$column))
                    ->schema([
                        Forms\Components\DatePicker::make($column.'_from')
                            //->before($date.'_until')
                            ->beforeOrEqual(fn (Forms\Get $get) => filled($get($column.'_until')) ? $column.'_until' : null)
                            ->debounce()
                            ->label(__('From')),
                        Forms\Components\DatePicker::make($column.'_until')
                            ->afterOrEqual(fn (Forms\Get $get) => filled($get($column.'_from')) ? $column.'_from' : null)
                            ->debounce()
                            ->label(__('Until')),
                    ])->columnSpanFull()->columns(2),
            ])->columnSpan(2)
            ->query(function (Builder $query, array $data) use ($column) {
                return $query->when($data[$column.'_from'],
                    fn ($query) => $query->where($column, '>=', $data[$column.'_from']))
                    ->when($data[$column.'_until'],
                        fn ($query) => $query->where($column, '<=', $data[$column.'_until']));
            });
    }
}
