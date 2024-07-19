<?php

namespace App\Filament\Resources\Promotion\SaleResource\Pages;

use App\Filament\Resources\Promotion\SaleResource;
use App\Helpers\Promotion\Sales\SaleHelper;
use App\Services\Iotron\MoneyService\Money;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $sale = $this->record->toArray();


        $this->saleHelper = new SaleHelper();
        $this->conditions = $this->saleHelper->getCondition();
        $this->form->fill(array_merge($sale));
        // $this->fillForm();
    }




    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([

                Section::make('General Information')
                    ->aside()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),
                        IconEntry::make('status')->boolean(),
                        TextEntry::make('discount_amount')->money(Money::defaultCurrency()),
                    ]),

                Section::make('Description')
                    ->aside()
                    ->schema([
                        TextEntry::make('description')->alignJustify()->hiddenLabel()->columnSpanFull(),
                    ]),


            ]);
    }














}
