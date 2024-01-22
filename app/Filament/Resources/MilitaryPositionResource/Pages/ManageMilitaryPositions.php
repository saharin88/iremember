<?php

namespace App\Filament\Resources\MilitaryPositionResource\Pages;

use App\Filament\Resources\MilitaryPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMilitaryPositions extends ManageRecords
{
    protected static string $resource = MilitaryPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('Military positions');
    }
}
