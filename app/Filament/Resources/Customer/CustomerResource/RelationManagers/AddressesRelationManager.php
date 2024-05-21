<?php

namespace App\Filament\Resources\Customer\CustomerResource\RelationManagers;

use App\Models\Localization\Country;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // address form section
                Fieldset::make(__('Address Detail'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->maxLength(100)
                            ->placeholder('Enter Name For This Address')
                            ->helperText('name for this address eg. my home')
                            ->required(),

                        TextInput::make('contact')
                            ->tel()
                            ->placeholder(__('Enter Contact Number'))
//                            ->mask(
//                                fn (TextInput\Mask $mask) => $mask->numeric()
//                                    ->integer()
//                                    ->minValue(1000000000)
//                                    ->maxValue(999999999999999)
//                            )
                            ->helperText('your default mobile or telephone number')
                            ->required(),

                        TextInput::make('alternate_contact')
                            ->label(__('Alternate Contact'))
                            ->placeholder(__('Enter Alternative Contact Number'))
                            ->tel()
//                            ->mask(
//                                fn (TextInput\Mask $mask) => $mask->numeric()
//                                    ->integer()
//                                    ->minValue(1000000000)
//                                    ->maxValue(999999999999999)
//                            )
                            ->helperText('your alternative mobile or telephone number')
                            ->columnSpan(1),

                        Radio::make('type')
                            ->label(__('Address Type'))
                            ->inline()
                            ->default('Home')
                            ->options([
                                'Home' => 'Home',
                                'Work' => 'Work',
                                'Other' => 'Other',
                            ])
                            ->helperText(__('choose address type'))
                            ->required(),

                        Toggle::make('default')
                            ->label(__('Make Default'))
                            ->inline()
                            ->helperText(__('Set this address as default for future use'))
                            ->default(true)
                            ->required(),

                    ])->columns(2),

                Fieldset::make('Address Line')->label(__('Address Line'))
                    ->schema([
                        TextInput::make('address_1')
                            ->required()
                            ->maxLength(200),
                        TextInput::make('address_2')
                            ->maxLength(200),
                    ])->columns(1),

                Fieldset::make('Description')->label(__('Description'))
                    ->schema([
                        TextInput::make('landmark')
                            ->maxLength(100),

                        TextInput::make('city')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('postal_code')
//                            ->mask(
//                                fn (TextInput\Mask $mask) => $mask->numeric()
//                                    ->integer()
//                                    ->minValue(100000)
//                                    ->maxValue(9999999999)
//                            )
                            ->required(),

                        TextInput::make('state')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('priority')
                            ->label(__('priority'))
                            ->default(1)
//                            ->mask(
//                                fn (TextInput\Mask $mask) => $mask->numeric()
//                                    ->integer()
//                                    ->minValue(1)
//                                    ->maxValue(10000)
//
//                            )
                            ->required(),

                        Select::make('country_code')
                            ->label(__('Country'))
                            ->options(Country::where('status', true)->get()->pluck('name', 'iso_code_2'))
                            ->required(),

                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
