<?php

namespace App\Filament\Resources\MemorialResource\Pages;

use App\Filament\Resources\MemorialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMemorial extends CreateRecord
{
    protected static string $resource = MemorialResource::class;

    public function getTitle(): string
    {
        return __('Create');
    }
}
