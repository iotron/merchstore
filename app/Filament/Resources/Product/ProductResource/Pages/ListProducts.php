<?php

namespace App\Filament\Resources\Product\ProductResource\Pages;

use App\Filament\Resources\Product\ProductResource;
use App\Models\Enums\Product\ProductTypeCast;
use App\Models\Product\Product;
use App\Services\MoneyServices\Money;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-m-plus-circle')
                ->label(__('New Product')),
        ];
    }


    public function getTabs(): array
    {
        return [
            'simple' => Tab::make()
                ->icon(ProductTypeCast::SIMPLE->getIcon())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type','=',ProductTypeCast::SIMPLE->value);
                }),
            'configurable' => Tab::make()
                ->icon(ProductTypeCast::CONFIGURABLE->getIcon())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type','=',ProductTypeCast::CONFIGURABLE->value);
                }),
            'all' => Tab::make()->icon('heroicon-m-queue-list'),
        ];
    }


    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->latest())
            ->contentGrid(['md' => 2,'lg' => 3])
            ->columns([

                Stack::make([
                    Tables\Columns\Layout\Grid::make(['md' => 3])
                        ->columns(3)
                        ->schema([

                            SpatieMediaLibraryImageColumn::make('thumb')
                                ->square()
                                ->size('70%')
                                ->alignCenter()
                                ->extraImgAttributes(['class' => 'rounded-xl mx-auto'])
                                ->columnSpan(1)
                                ->collection('productDisplay'),

                            Stack::make([
                                TextColumn::make('name')
                                    ->label('Name')->searchable()
                                    ->size(TextColumn\TextColumnSize::Large)
                                    ->weight(FontWeight::Medium)
                                    ->color('primary'),


                                Tables\Columns\Layout\Split::make([
                                    TextColumn::make('type')->label('Type')->sortable()->toggleable(),

                                    TextColumn::make('status')
                                        ->label('Status')
                                        ->sortable()
                                        ->alignRight()
                                        ->badge(),
                                ])->columnSpanFull(),


                                TextColumn::make('sku')->prefix('SKU : ')->searchable(),

                                Tables\Columns\Layout\Split::make([
                                    TextColumn::make('price')->prefix('Price : ')
                                        ->money(Money::defaultCurrency())->sortable()
                                        ->weight(FontWeight::Medium)
                                        ->toggleable()->toggledHiddenByDefault(),

                                    TextColumn::make('quantity')
                                        ->badge()->color('info')
                                        ->alignRight()
                                        ->prefix('Qty : ')->toggleable(),
                                ])->columnSpanFull()

                            ])
                                ->columnSpan(2),

                        ])->extraAttributes(['class' => 'mb-2']),

                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('attribute_group.code')
                            ->description('Group')->alignCenter()
                            ->default('--not provided yet--')
                            ->sortable()->toggleable()->toggledHiddenByDefault(),

                        TextColumn::make('parent.name')
                            ->description('Parent')->alignCenter()
                            ->default('Root Product')
                            ->sortable(),
                    ]),

                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('view_count')->default(0)->description('Views')->alignCenter()->toggleable()->sortable(),
                        TextColumn::make('popularity')->default(0)->description('Popularity')->alignCenter()->toggleable()->sortable(),
                    ]),


                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('created_at')
                            ->description('Create On')
                            ->since()->toggleable()->toggledHiddenByDefault(),
                        TextColumn::make('updated_at')
                            ->description('Modified On')
                            ->alignRight()
                            ->since()->toggleable()->toggledHiddenByDefault(),
                    ]),

                ]),









            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(collect(ProductTypeCast::cases())
                        ->mapWithKeys(fn(ProductTypeCast $type) => [$type->value => $type->getLabel()])
                        ->toArray()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
