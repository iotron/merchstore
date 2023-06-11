<?php

namespace App\Filament\Resources\Attribute;

use App\Filament\Resources\Attribute\AttributeResource\Pages;
use App\Filament\Resources\Attribute\AttributeResource\RelationManagers;
use App\Models\Attribute\Attribute;
use Filament\Forms;
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
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('admin_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('desc')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('validation'),
                Forms\Components\TextInput::make('position'),
                Forms\Components\Toggle::make('is_filterable')
                    ->required(),
                Forms\Components\Toggle::make('is_configurable')
                    ->required(),
                Forms\Components\Toggle::make('is_user_defined')
                    ->required(),
                Forms\Components\Toggle::make('is_required')
                    ->required(),
                Forms\Components\Toggle::make('is_visible_on_front')
                    ->required(),
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
            //
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
