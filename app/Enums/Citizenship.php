<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Citizenship: string implements HasLabel
{
    case UA = 'UA';
    case OTHER = 'OTHER';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UA => __('Ukraine'),
            self::OTHER => __('Other'),
        };
    }
}
