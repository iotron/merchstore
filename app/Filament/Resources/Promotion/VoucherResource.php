<?php

namespace App\Filament\Resources\Promotion;

use App\Filament\Resources\Promotion\VoucherResource\Pages;
use App\Filament\Resources\Promotion\VoucherResource\RelationManagers;
use App\Models\Promotion\Voucher;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationGroup = 'Promotion';

    protected static ?string $navigationIcon = 'heroicon-o-collection';


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
