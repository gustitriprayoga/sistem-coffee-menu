<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriMenuResource\Pages;
use App\Filament\Resources\KategoriMenuResource\RelationManagers;
use App\Models\KategoriMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Traits\HasRoles;

class KategoriMenuResource extends Resource
{
    protected static ?string $model = KategoriMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Menu';
    protected static ?string $navigationLabel = 'Kategori';

    public static function canViewAny(): bool
    {
        // Admin dan Karir bisa melihat daftar pesanan
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    public static function canCreate(): bool
    {
        // Hanya Admin yang bisa membuat pesanan baru dari panel
        return auth()->user()->hasRole('admin');

    }
    public static function canEdit($record): bool
    {
        // Hanya Admin yang bisa mengedit
        return auth()->user()->hasRole('admin');
    }


    public static function canDelete(Model $record): bool
    {
        // Hanya Admin yang bisa menghapus
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListKategoriMenus::route('/'),
            'create' => Pages\CreateKategoriMenu::route('/create'),
            'edit' => Pages\EditKategoriMenu::route('/{record}/edit'),
        ];
    }
}
