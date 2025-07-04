<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Doctrine\DBAL\Query\SelectQuery;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Menu';

    protected static ?string $navigationLabel = 'Menu';

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

    public static function canDelete($record): bool
    {
        // Hanya Admin yang bisa menghapus
        return auth()->user()->hasRole('admin');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kategori_menu_id')
                    ->label('Kategori')
                    ->placeholder('pilih kategori menu')
                    ->relationship('kategori', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->label('Harga')
                    ->numeric(),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                    ->label('Stok')
                    ->numeric()
                    ->default(0),
                Forms\Components\FileUpload::make('gambar')
                    ->imageEditor()
                    ->required()
                    ->maxSize(2048)
                    ->directory('menu-images'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gambar')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? '<img src="' . asset('storage/' . $state) . '" alt="Menu Image" class="w-16 h-16 object-cover">' : 'No Image')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            // Define any relations here if needed
            // For example, if you have a relation manager for orders:
            // RelationManagers\OrdersRelationManager::class,
            // RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
