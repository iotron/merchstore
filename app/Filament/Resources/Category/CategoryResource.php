<?php

namespace App\Filament\Resources\Category;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\CategoryResource\Pages;
use App\Models\Category\Category;
use Filament\Resources\Resource;

class CategoryResource extends Resource
{
    use HasAdjacencyFormSchema;

    protected static ?string $model = Category::class;

    protected static ?string $navigationGroup = 'Product Management';

    protected static ?string $slug = 'category';

    protected static ?string $recordRouteKeyName = 'url';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getForm(): array
    {
        return self::getAdjacencyResourceFormSchema();
    }

    public static function getParentForm(): array
    {
        return self::getAdjacencyResourceParentFormSchema();
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
            'view' => Pages\ViewCategory::route('/{record:url}'),
            'edit' => Pages\EditCategory::route('/{record:url}/edit'),
        ];
    }
}
