<?php

namespace App\Filament\Helpers;

use Filament\Tables;
use Filament\Tables\Table;

final class Trashed
{
    public static function actions(): array
    {
        return [
            Tables\Actions\RestoreAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            Tables\Actions\ForceDeleteBulkAction::make()
                ->visible(fn (Table $table) => self::filtered($table)),
            Tables\Actions\RestoreBulkAction::make()
                ->visible(fn (Table $table) => self::filtered($table)),
        ];
    }

    public static function filtered(Table $table): bool
    {
        return isset($table->getFiltersForm()->getState()['trashed']['value'])
            && $table->getFiltersForm()->getState()['trashed']['value'] === '0';
    }
}
