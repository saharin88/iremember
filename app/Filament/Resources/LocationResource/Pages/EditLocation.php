<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Traits\AuditEditRecordTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocation extends EditRecord
{
    use AuditEditRecordTrait {
        afterSave as protected afterSaveTrait;
    }

    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return __('Edit');
    }

    protected function afterSave(): void
    {
        $this->refreshFormData(['slug']);
        $this->afterSaveTrait();
    }
}
