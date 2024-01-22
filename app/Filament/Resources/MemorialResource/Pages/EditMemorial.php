<?php

namespace App\Filament\Resources\MemorialResource\Pages;

use App\Filament\Resources\MemorialResource;
use App\Traits\AuditEditRecordTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemorial extends EditRecord
{
    use AuditEditRecordTrait {
        afterSave as protected afterSaveTrait;
    }

    protected static string $resource = MemorialResource::class;

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
