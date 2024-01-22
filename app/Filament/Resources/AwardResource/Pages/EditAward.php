<?php

namespace App\Filament\Resources\AwardResource\Pages;

use App\Filament\Resources\AwardResource;
use App\Traits\AuditEditRecordTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAward extends EditRecord
{
    use AuditEditRecordTrait {
        afterSave as protected afterSaveTrait;
    }

    protected static string $resource = AwardResource::class;

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
