<?php

namespace App\Filament\Resources\Promotion;

use App\Filament\Resources\Promotion\VoucherResource\Pages;
use App\Filament\Resources\Promotion\VoucherResource\RelationManagers;
use App\Models\Promotion\Voucher;
use Filament\Resources\Resource;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationGroup = 'Promotion';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
            RelationManagers\CouponsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'view' => Pages\ViewVoucher::route('/{record}'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
