<?php

namespace App\Filament\Resources\Category;

use App\Filament\Common\Schema\AdjacencySchema\HasAdjacencyFormSchema;
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
    use HasAdjacencyFormSchema;
    protected static ?string $model = Theme::class;

    protected static ?string $slug = 'themes';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordRouteKeyName = 'url';


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
            'index' => Pages\ListThemes::route('/'),
            'create' => Pages\CreateTheme::route('/create'),
            'edit' => Pages\EditTheme::route('/{record:url}/edit'),
            'view' => Pages\ViewThemes::route('/{record:url}'),
        ];
    }
}
