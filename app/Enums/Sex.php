<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Sex: int implements HasLabel
{
    case FEMALE = 0;

    case MALE = 1;

    public function getLabel(): ?string
    {
        return __($this->name);
    }
}
