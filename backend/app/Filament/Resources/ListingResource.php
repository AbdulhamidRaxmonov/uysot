<?php

namespace App\Filament\Resources;

use App\Models\Listing;
use Filament\Resources\Resource;
use Filament\Resources\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function form(Forms\Components\Form $form): Forms\Components\Form
    {
        return $form->schema([
            TextInput::make('title')->required(),
            TextInput::make('price'),
            TextInput::make('city'),
            TextInput::make('address'),
            TextInput::make('currency')->default('UZS'),
            Textarea::make('description'),
        ]);
    }

    public static function table(Tables\Columns\Table $table): Tables\Columns\Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('title')->searchable()->limit(30),
            TextColumn::make('price'),
            TextColumn::make('city'),
            BadgeColumn::make('is_vip')->label('VIP')->colors(['primary' => 'true']),
        ])->filters([])->actions([])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
