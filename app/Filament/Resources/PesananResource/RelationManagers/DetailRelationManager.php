<?php

namespace App\Filament\Resources\PesananResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailRelationManager extends RelationManager
{
    protected static string $relationship = 'detail';
    protected static ?string $title = 'Detail Pesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pesanan_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('menu_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kuantitas')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Detail Pesanan')
            ->columns([
                TextColumn::make('pesanan_id')
                    ->label('ID Pesanan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('menu_id')
                    ->label('ID Menu')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kuantitas')
                    ->label('Kuantitas')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
