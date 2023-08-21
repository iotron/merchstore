<?php

namespace App\Filament\Resources\Category;

use App\Filament\Resources\Category\CategoryResource\Pages;
use App\Filament\Resources\Category\CategoryResource\RelationManagers;
use App\Models\Category\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?string $slug = 'category';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Manage')
                    ->schema([
                        Forms\Components\Toggle::make('status')
                            ->required(),
                        Forms\Components\Toggle::make('is_visible_on_front')
                            ->required(),
                        Forms\Components\TextInput::make('order')->inlineLabel()->integer()->minValue(0)->maxValue(999999),
                    ])->columns(3),


                Forms\Components\Section::make('General Info')
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
                            ->label(__('Choose Parent Category')),

                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->columnSpan(3)
                            ->hint(__('Max: 100'))
                            ->maxLength(100),

                        TiptapEditor::make('desc')
                            ->columnSpanFull()
                            ->hint(__('Max: 30,000'))
                            ->maxLength(30000),

                    ])->columns(3),



                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Repeater::make('meta_data')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->hint(__('Max: 100'))
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('value')
                                    ->hint(__('Max: 1000'))
                                    ->maxLength(1000),
                            ])->columns(2)->columnSpanFull(),
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
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_visible_on_front')
                    ->boolean(),
                Tables\Columns\TextColumn::make('view_count'),
                Tables\Columns\TextColumn::make('order'),


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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
