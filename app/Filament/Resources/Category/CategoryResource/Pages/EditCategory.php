<?php

namespace App\Filament\Resources\Category\CategoryResource\Pages;

use App\Filament\Resources\Category\CategoryResource;
use App\Models\Category\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Saade\FilamentAdjacencyList\Forms\Components\AdjacencyList;


class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }



    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema(array_merge(
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
            ));
    }








}
