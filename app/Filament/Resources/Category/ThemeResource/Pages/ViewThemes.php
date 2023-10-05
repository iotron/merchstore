<?php

namespace App\Filament\Resources\Category\ThemeResource\Pages;

use App\Filament\Resources\Category\ThemeResource;
use Filament\Actions;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewThemes extends ViewRecord
{
    protected static string $resource = ThemeResource::class;

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
                        TextEntry::make('parent.name')->label('Parent'),
                        TextEntry::make('name')->label('Name'),
                        TextEntry::make('url')->label('URL')
                            ->formatStateUsing(function ($record) {
                                return $record->url;
                            })
                            ->copyable()
                            ->copyableState(function ($record) {
                                return $record->url;
                            })
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
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
