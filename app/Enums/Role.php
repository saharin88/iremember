<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Role: int implements HasColor, HasLabel
{
    case ADMINISTRATOR = 1;

    case EDITOR = 2;

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADMINISTRATOR => 'danger',
            self::EDITOR => 'success',
        };
    }
}
