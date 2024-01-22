<?php

namespace App\Filament\RelationManagers;

use Filament\Tables\Table;

class AuditsRelationManager extends \Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns($this->addToggleable($table->getColumns()))
            ->emptyStateHeading(__('No audits found'));
    }

    private function addToggleable($columns)
    {
        if (is_array($columns)) {
            return array_map(function ($column) {
                return $column->toggleable();
            }, $columns);
        }

        return $columns->toggleable();
    }
}
