<?php

namespace App\Filament\Resources\PersonResource\Pages;

use App\Filament\Resources\PersonResource;
use App\Traits\AuditEditRecordTrait;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerson extends EditRecord
{
    use AuditEditRecordTrait {
        afterSave as protected afterSaveTrait;
    }

    protected static string $resource = PersonResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('save')
                ->translateLabel()
                ->action('save'),
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
