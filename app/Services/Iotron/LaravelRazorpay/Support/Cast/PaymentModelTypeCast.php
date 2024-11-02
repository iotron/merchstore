<?php

namespace App\Services\Iotron\LaravelRazorpay\Support\Cast;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentModelTypeCast: string implements HasColor, HasIcon, HasLabel
{
    case STANDARD = 'standard';
    case LINK = 'payment_link';
    case QR = 'qr';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::STANDARD => 'Standard',
            self::LINK => 'Payment Link',
            self::QR => 'QR',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::STANDARD => 'primary',
            self::LINK => 'warning',
            self::QR => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::STANDARD => 'heroicon-m-pencil',
            self::LINK => 'heroicon-m-eye',
            self::QR => 'heroicon-m-cursor-arrow-rays',
        };
    }
}
