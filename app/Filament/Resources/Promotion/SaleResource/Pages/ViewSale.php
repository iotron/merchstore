<?php

namespace App\Filament\Resources\Promotion\SaleResource\Pages;

use App\Filament\Resources\Promotion\SaleResource;
use App\Helpers\Promotion\Sales\SaleHelper;
use Filament\Actions\EditAction;
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
        $sale['discount_amount'] = $sale['discount_amount']->getAmount();

        $this->saleHelper = new SaleHelper();
        $this->conditions = $this->saleHelper->getCondition();
        $this->form->fill(array_merge($sale));
        // $this->fillForm();
    }
}
