<?php

namespace App\Filament\Resources\Attribute;

use App\Filament\Resources\Attribute\AttributeResource\Pages;
use App\Filament\Resources\Attribute\AttributeResource\RelationManagers;
use App\Models\Attribute\Attribute;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;
    protected static ?string $navigationGroup = 'Filters';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make(__('Information'))
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->placeholder(__('Enter Code'))
                            ->columnSpan(2)
                            ->maxLength(255),

                        TextInput::make('type')
                            ->required()
                            ->placeholder(__('Enter Type'))
                            ->columnSpan(2)
                            ->maxLength(255),

                        TextInput::make('admin_name')
                            ->label(__('Name'))
                            ->placeholder(__('Enter Name'))
                            ->required()
                            ->columnSpan(3)
                            ->maxLength(255),

                        TextInput::make('position')
                            ->placeholder(__('Enter Position'))
                            ->mask(
                                fn (TextInput\Mask $mask) => $mask
                                    ->numeric()
                                    ->decimalPlaces(0)
                                    ->decimalSeparator('.')
                                    ->minValue(1)
                                    ->maxValue(999999)
                                    ->thousandsSeparator(',')
                            )
                            ->required()
                            ->columnSpan(1)
                            ->label(__('Position')),

                    ])->columns(4),

                Fieldset::make(__('Description'))
                    ->schema([

                        Textarea::make('desc')
                            ->label('Description')
                            ->placeholder(__('Enter Description'))
                            ->required()
                            ->maxLength(255),

                        Textarea::make('validation')
                            ->placeholder(__('Enter Validation '))
                            ->nullable()
                            ->maxLength(255),

                    ])->columns(2),

                Fieldset::make(__('Manage'))
                    ->schema([
                        Toggle::make('is_filterable')
                            ->label(__('Filterable'))
                            ->default(false)
                            ->required(),

                        Toggle::make('is_configurable')
                            ->label(__('Configurable'))
                            ->default(false)
                            ->required(),

                        Toggle::make('is_user_defined')
                            ->label(__('User Define'))
                            ->default(true)
                            ->required(),

                        Toggle::make('is_required')
                            ->label(__('Required'))
                            ->default(true)
                            ->required(),

                        Toggle::make('is_visible_on_front')
                            ->label(__('Visibility'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('admin_name')->label(__('Name')),
                Tables\Columns\TextColumn::make('type'),
                //                Tables\Columns\TextColumn::make('position'),
                Tables\Columns\BooleanColumn::make('is_filterable')->label(__('Filterable'))->toggleable(),
                Tables\Columns\BooleanColumn::make('is_configurable')->label(__('Configurable'))->toggleable(),
                Tables\Columns\BooleanColumn::make('is_user_defined')->label(__('User Define'))->toggleable(),
                Tables\Columns\BooleanColumn::make('is_required')->label(__('Required'))->toggleable(),
                Tables\Columns\BooleanColumn::make('is_visible_on_front')->label(__('Visibility'))->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OptionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'view' => Pages\ViewAttribute::route('/{record}'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
