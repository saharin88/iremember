<?php

namespace App\Filament\Resources\BattleResource\Pages;

use App\Filament\Resources\BattleResource;
use App\Traits\AuditEditRecordTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBattle extends EditRecord
{
    use AuditEditRecordTrait {
        afterSave as protected afterSaveTrait;
    }

    protected static string $resource = BattleResource::class;

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
