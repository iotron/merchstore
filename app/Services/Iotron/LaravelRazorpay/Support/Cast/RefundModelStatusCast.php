<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Cast;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RefundModelStatusCast: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case FAILED = 'failed';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSED => 'Refunded',
            self::FAILED => 'Failed',

        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'primary',
            self::PROCESSED => 'success',
            self::FAILED => 'danger',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {

            self::PENDING => 'heroicon-m-pencil',
            self::PROCESSED => 'heroicon-m-eye',
            self::FAILED => 'heroicon-m-cursor-arrow-rays',

        };
    }
}
