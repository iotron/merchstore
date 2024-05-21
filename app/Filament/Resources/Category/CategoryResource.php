<?php

namespace App\Filament\Resources\Category;

use App\Filament\Resources\Category\CategoryResource\Pages;
use App\Models\Category\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\HtmlString;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Product Management';

    protected static ?string $slug = 'category';
    protected static ?string $recordRouteKeyName = 'url';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//
//                Forms\Components\Section::make('Manage')
//                    ->schema([
//                        Forms\Components\Toggle::make('status')
//                            ->required(),
//                        Forms\Components\Toggle::make('is_visible_on_front')
//                            ->required(),
//                        Forms\Components\TextInput::make('order')->inlineLabel()->integer()->minValue(0)->maxValue(999999),
//                    ])->columns(3),
//
//                Forms\Components\Section::make('Media')
//                    ->schema([
//                        SpatieMediaLibraryFileUpload::make('categoryGallary')
//                            ->multiple()
//                            ->collection('categoryGallary')
//                            ->columnSpan(3)
//                            ->reorderable(),
//                    ])->columns(3),
//
//                Forms\Components\Section::make('General Info')
//                    ->schema([
//
//                        Forms\Components\TextInput::make('name')
//                            ->required()
//                            ->columnSpan(2)
//                            ->hint(__('Max: 100'))
//                            ->maxLength(100),
//
//                        Forms\Components\Select::make('parent_id')
//                            ->relationship('parent', 'name')
//                            ->nullable()
//                            ->columnSpan(1)
//                            ->label(__('Choose Parent Category')),
//
//                        Forms\Components\TextInput::make('url')
//                            ->required()
//                            ->columnSpan(3)
//                            ->hint(__('Max: 100'))
//                            ->maxLength(100),
//
//                        TiptapEditor::make('desc')
//                            ->columnSpanFull()
//                            ->hint(__('Max: 30,000'))
//                            ->maxContentWidth(30000),
//
//                    ])->columns(3),
//
//                Forms\Components\Section::make('SEO')
//                    ->schema([
//                        Forms\Components\Repeater::make('meta_data')
//                            ->schema([
//                                Forms\Components\TextInput::make('key')
//                                    ->hint(__('Max: 100'))
//                                    ->maxLength(100),
//                                Forms\Components\Textarea::make('value')
//                                    ->hint(__('Max: 1000'))
//                                    ->maxLength(1000),
//                            ])->columns(2)->columnSpanFull(),
//                    ]),
//
//                Forms\Components\Section::make('Banners')
//                    ->schema([
//                        Forms\Components\Repeater::make('banners')
//                            ->schema([
//                                Forms\Components\TextInput::make('link')
//                                    ->url(),
//                                SpatieMediaLibraryFileUpload::make('banner')
//                                    ->multiple()
//                                    ->collection('banners')
//                                    ->columnSpan(3),
//                            ]),
//                    ]),
//
//            ]);
//    }



    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->lazy()
                ->afterStateUpdated(function (Set $set, $state) {
                    $set('url', Str::slug($state));
                })
                ->hint(__('Max: 100'))
                ->maxLength(100),

            Forms\Components\TextInput::make('url')
                ->required()->unique(ignoreRecord: true)
                ->hint(__('Max: 150'))
                ->maxLength(150),

            Forms\Components\Toggle::make('status')
                ->default(false)
                ->required(),
        ];
    }

    public static function getParentForm(): array
    {
        return [
            Forms\Components\Toggle::make('show_parent')->label('Add/modify parent')->dehydrated(false)->live()
                ->helperText(fn (): HtmlString => new HtmlString('<ol class="font-semibold">
                        <li>Dont add parent for root levels.</li>
                        <li>Use the x icon to remove parent.</li>
                        </ol>')),

            SelectTree::make('parent_id')->searchable()->withCount()
                ->relationship('parent', 'url', 'parent_id')->visible(fn (Forms\Get $get): bool => $get('show_parent') ?? false),

        ];
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
