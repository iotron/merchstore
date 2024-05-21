<?php

namespace App\Filament\Common\Schema\AdjacencySchema;

use App\Models\Category\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;

trait HasAdjacencyFormSchema
{


    public function getAdjacencyFormSchema(): array
    {
        return array_merge(
            self::$resource::getForm(),
            self::$resource::getParentForm(),
            [
                Forms\Components\SpatieMediaLibraryFileUpload::make('category_image')
                    ->hint('max 1mb / aspect 1:1')
                    ->image()
                    ->maxSize(1024)
                    ->imageCropAspectRatio('1:1')
                    ->collection(Category::CATEGORY_IMAGE)
                    ->responsiveImages(),

                AdjacencyList::make('children')->columnSpanFull()
                    ->relationship('descendants')
                    ->form(self::$resource::getForm())
                    ->labelKey('url')
                    ->maxDepth(2)
                    ->addable()
                    ->editable()
                    ->deletable(),
            ]
        );
    }







    /**
     * This Part For Resource Methods
     * Required For Form.
     * Placed In Resource
     */



    public static function getAdjacencyResourceFormSchema(): array
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

    public static function getAdjacencyResourceParentFormSchema(): array
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

}
