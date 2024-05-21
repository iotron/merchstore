<?php

namespace App\Filament\Resources\Category;

use App\Filament\Resources\Category\ThemeResource\Pages;
use App\Models\Category\Theme;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static ?string $slug = 'themes';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Req')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(2)
                            ->hint(__('Max: 100'))
                            ->maxLength(100),

                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->nullable()
                            ->columnSpan(1)
                            ->label(__('Choose Parent Theme')),

                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->columnSpan(3)
                            ->hint(__('Max: 100'))
                            ->maxLength(100),

                    ]),

                Forms\Components\Section::make('Banners')
                    ->schema([
                        Forms\Components\Repeater::make('banners')
                            ->schema([
                                Forms\Components\TextInput::make('link')
                                    ->url(),
                                SpatieMediaLibraryFileUpload::make('banner')
                                    ->multiple()
                                    ->collection('banners')
                                    ->columnSpan(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent.name'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('url'),
            ])
            ->filters([
                SelectFilter::make('Parent')
                    ->relationship('parent', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit' => Pages\EditTheme::route('/{record}/edit'),
            'view' => Pages\ViewThemes::route('/{record}'),
        ];
    }
}
