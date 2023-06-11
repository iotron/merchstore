<?php

namespace App\Filament\Resources\Customer;

use App\Filament\Resources\Customer\CustomerResource\Pages;
use App\Filament\Resources\Customer\CustomerResource\RelationManagers;
use App\Models\Customer\Customer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $slug = 'customers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make(__('General Information'))->schema([

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->columnSpanFull()
                        ->hint(__('Max : 255'))
                        ->placeholder(__('Enter Customer Name'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->columnSpan(2)
                        ->required()
                        ->hint(__('Max : 255'))
                        ->placeholder(__('Enter Email'))
                        ->maxLength(255),

                    Forms\Components\TextInput::make('contact')
                        ->columnSpan(1)
                        ->placeholder(__('Enter Contact'))
                        ->hint(__('Max : 15'))
                        ->maxLength(15),

                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->placeholder(__('Enter Password'))
                        ->hint(__('Max : 55'))
                        ->columnSpanFull()
                        ->maxLength(55),

                ])->columns(3),

                Forms\Components\Fieldset::make(__('Manage Information'))->schema([
                    Forms\Components\Toggle::make('contact_verified')
                        ->default(false)
                        ->required(),
                    Forms\Components\Toggle::make('email_verified')
                        ->default(false)
                        ->required(),

                    //                    Forms\Components\Toggle::make('status')
//                        ->required(),
                ])->columns(3),

                Forms\Components\Fieldset::make(__('Additional Information'))->schema([
                    Forms\Components\TextInput::make('whatsapp')
                        ->columnSpanFull()
                        ->placeholder(__('Type Whatsapp profile link'))
                        ->hint(__('Max : 255'))
                        ->maxLength(255),
                    Forms\Components\Textarea::make('alt_contact')
                        ->columnSpanFull()
                        ->placeholder(__('type customer alternate contact'))
                        ->hint(__('Max : 255'))
                        ->maxLength(255),
                ])->columns(2),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->toggleable()->searchable(),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('contact')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined!' : $state;
                })->toggleable(),
                TextColumn::make('whatsapp')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined!' : $state;
                })->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('alt_contact')->formatStateUsing(function ($state){
                    return is_null($state) ? 'undefined!' : $state;
                })->toggleable()->toggledHiddenByDefault(),

                IconColumn::make('contact_verified_at')
                    ->label(__('Contact Verified'))
                    ->boolean()
                    ->default(false)
                    ->sortable()->toggleable()->toggledHiddenByDefault(),
                IconColumn::make('email_verified_at')
                    ->label(__('Email Verified'))
                    ->boolean()
                    ->default(false)
                    ->sortable()->toggleable(),



                TextColumn::make('created_at')->label(__('Created On'))
                    ->dateTime()->since()->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('updated_at')->label(__('Updated On'))
                    ->dateTime()->since()->toggleable(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
