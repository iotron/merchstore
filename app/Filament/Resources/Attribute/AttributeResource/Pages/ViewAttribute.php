<?php

namespace App\Filament\Resources\Attribute\AttributeResource\Pages;

use App\Filament\Resources\Attribute\AttributeResource;
use App\Models\Filter\FilterGroup;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAttribute extends ViewRecord
{
    protected static string $resource = AttributeResource::class;

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
                        TextEntry::make('display_name')->label('Display Name'),
                        TextEntry::make('code')
                            ->formatStateUsing(function ($record) {
                                return $record->code;
                            })
                            ->copyable()
                            ->copyableState(function ($record) {
                                return $record->code;
                            })
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('type')
                            ->badge()
                            ->colors([
                                'secondary',
                                'primary' => FilterGroup::FILTERABLE,
                                'success' => FilterGroup::STATIC,

                            ]),
                    ]),
                Section::make('Booleans')
                    ->columns([
                        'default' => 2,
                        'md' => 3,
                    ])
                    ->schema([
                        IconEntry::make('is_filterable')->label(__('Filterable'))
                            ->boolean(),
                        IconEntry::make('is_configurable')->label(__('Configurable'))
                            ->boolean(),
                        IconEntry::make('is_user_defined')->label(__('User Define'))
                            ->boolean(),
                        IconEntry::make('is_required')->label(__('Required'))
                            ->boolean(),
                        IconEntry::make('is_visible_on_front')->label(__('Visibility'))
                            ->boolean(),  
                    ]),
                Section::make('Date')
                    ->columns([
                        'default' => 2,
                        'md' => 3,
                    ])
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
            ]);

    }
}
