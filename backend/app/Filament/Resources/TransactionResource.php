<?php

namespace App\Filament\Resources;

use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Resources\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms;
use Filament\Forms\Components\TextInput;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    public static function form(Forms\Components\Form $form): Forms\Components\Form
    {
        return $form->schema([
            TextInput::make('amount')->required(),
            TextInput::make('currency')->required(),
        ]);
    }

    public static function table(Tables\Columns\Table $table): Tables\Columns\Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('provider'),
            TextColumn::make('amount'),
            BadgeColumn::make('status'),
        ])->filters([])->actions([])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
