<?php

namespace App\Filament\Resources\PesananResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class DetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = 'Item Pesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('menu_id')
                    ->relationship('menu', 'nama')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Menu'),
                Forms\Components\TextInput::make('kuantitas')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('menu.nama')
            ->columns([
                TextColumn::make('menu.nama')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kuantitas')
                    ->label('Kuantitas'),
                TextColumn::make('harga')
                    ->label('Harga Satuan')
                    ->prefix('Rp')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '.', decimalSeparator: ','),
                TextColumn::make('')
                    ->label('Subtotal')
                    ->money('IDR')
                    ->getStateUsing(fn($record) => $record->kuantitas * $record->harga)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $menu = \App\Models\Menu::find($data['menu_id']);
                        if ($menu) {
                            $data['harga'] = $menu->harga;
                        }
                        return $data;
                    }),
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
