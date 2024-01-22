<?php

namespace App\Filament\Helpers;

use Filament\Forms;

final class Rank
{
    public static function form(): array
    {
        $schema = [
            Forms\Components\TextInput::make('name')
                ->unique(ignoreRecord: true)
                ->label(__('Name'))
                ->required()
                ->maxLength(255),
        ];

        return $schema;
    }
}
