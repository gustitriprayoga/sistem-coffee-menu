<?php

namespace App\Filament\Resources\PesananResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn; // Tambahkan ini

class DetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details'; // Pastikan ini sesuai dengan nama relasi di model Pesanan

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('menu.nama') // Mengambil nama menu dari relasi
                    ->label('Menu')
                    ->disabled(), // Tidak bisa diubah
                Forms\Components\TextInput::make('kuantitas')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly(), // Harga satuan tidak bisa diubah dari sini
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('menu.nama') // Untuk judul record
            ->columns([
                TextColumn::make('menu.nama')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('harga')
                    ->label('Harga Satuan')
                    ->prefix('Rp')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '.', decimalSeparator: ','),
                TextColumn::make('kuantitas')
                    ->label('Kuantitas'),
                TextColumn::make('subtotal') // Menampilkan subtotal yang dihitung
                    ->label('Subtotal')
                    ->money('IDR') // Atau mata uang lain sesuai kebutuhan
                    ->getStateUsing(fn ($record) => $record->kuantitas * $record->harga),
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
