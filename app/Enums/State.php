<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum State: int implements HasColor, HasIcon, HasLabel
{
    case UNPUBLISHED = 0;
    case PUBLISHED = 1;

    public function getLabel(): ?string
    {
        return __($this->name);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNPUBLISHED => 'danger',
            self::PUBLISHED => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::UNPUBLISHED => 'heroicon-o-x-circle',
            self::PUBLISHED => 'heroicon-o-check-circle',
        };
    }
}
