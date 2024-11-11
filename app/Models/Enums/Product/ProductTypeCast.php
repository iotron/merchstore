<?php

namespace App\Models\Enums\Product;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductTypeCast: string implements HasColor, HasIcon, HasLabel
{
    case SIMPLE = 'simple';
    case CONFIGURABLE = 'configurable';

    /**
     * @return string|array|null
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SIMPLE => 'success',
            self::CONFIGURABLE => 'info',
        };
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::SIMPLE => 'heroicon-o-cube',
            self::CONFIGURABLE => 'heroicon-o-adjustments-vertical',
        };
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::SIMPLE => 'Simple Product',
            self::CONFIGURABLE => 'Configurable Product',
        };
    }
}
