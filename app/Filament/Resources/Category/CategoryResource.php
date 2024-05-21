<?php

namespace App\Filament\Resources\Category;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
use App\Filament\Resources\Category\CategoryResource\Pages;
use App\Models\Category\Category;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
