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
                TextColumn::make('menu.nama')->label('Menu'),
                TextColumn::make('kuantitas')->label('Qty'),
                TextColumn::make('harga')->money('IDR')->label('Harga Satuan'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->getStateUsing(fn($record) => $record->kuantitas * $record->harga)
                    ->money('IDR'),
            ])
            ->headerActions([]) // disable tambah
            ->actions([])       // disable edit/hapus
            ->bulkActions([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Detail Pesanan')
            ->columns([
                Tables\Columns\TextColumn::make('Detail Pesanan'),
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
