<?php

namespace App\Filament\Resources\Attribute\AttributeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $recordTitleAttribute = 'admin_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('admin_name')
                    ->label(__('Name'))
                    ->placeholder(__('Enter Option Name'))
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->minLength(3),

                TextInput::make('swatch_value')
                    ->label(__('Swatch'))
                    ->placeholder(__('Enter Swatch Value'))
                    ->nullable()
                    ->integer()
                    ->columnSpan(1)
                    ->maxValue(10000)
                    ->minValue(0),

                TextInput::make('position')
                    ->label(__('Position'))
                    ->placeholder(__('Enter Position'))
                    ->nullable()
                    ->integer()
                    ->columnSpan(1)
                    ->maxValue(10000)
                    ->minValue(0),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('admin_name'),
                Tables\Columns\TextColumn::make('swatch_value')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined' : $state;
                }),
                Tables\Columns\TextColumn::make('position')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined' : $state;
                }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->disableCreateAnother()->label(__('New Option')),
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
