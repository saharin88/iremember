<?php

namespace App\Filament\Resources\MilitaryBranchResource\Pages;

use App\Filament\Resources\MilitaryBranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMilitaryBranches extends ManageRecords
{
    protected static string $resource = MilitaryBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('Military branches');
    }
}
