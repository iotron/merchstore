<?php

namespace App\Filament\Resources\Attribute\AttributeGroupResource\Pages;

use App\Filament\Resources\Attribute\AttributeGroupResource;
use App\Models\Filter\FilterGroup;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAttributeGroup extends ViewRecord
{
    protected static string $resource = AttributeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make('Main Info')
                    ->columns([
                        'default' => 2,
                        'md' => 3,
                    ])->columnSpan(1)
                    ->schema([
                        TextEntry::make('admin_name')->label('Admin'),
                        TextEntry::make('code')
                            ->label('Code')
                            ->copyable()
                            ->copyMessage('Code Copied !'),
                        TextEntry::make('type')
                            ->badge()
                            ->colors([
                                'secondary',
                                'primary' => FilterGroup::FILTERABLE,
                                'success' => FilterGroup::STATIC,

                            ]),
                        TextEntry::make('position')->label('Position'),
                        TextEntry::make('updated_at')->label('Modified On')->since(),

                    ]),

            ]);
    }


}
