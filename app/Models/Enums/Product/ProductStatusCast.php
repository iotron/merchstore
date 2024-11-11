<?php

namespace App\Models\Enums\Product;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductStatusCast: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case REVIEW = 'review';
    case PUBLISHED = 'published';

    /**
     * @return string|array|null
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::REVIEW => 'info',
            self::PUBLISHED => 'success',
        };
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-document',
            self::REVIEW => 'heroicon-o-eye',
            self::PUBLISHED => 'heroicon-o-check-circle',
        };
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::REVIEW => 'Under Review',
            self::PUBLISHED => 'Published',
        };
    }
}
