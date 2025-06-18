<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Filament\Resources\PesananResource\RelationManagers\DetailRelationManager;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    // protected static ?string $navigationGroup = 'Manajemen Pesanan';
    protected static ?string $navigationLabel = 'Pesanan Masuk';

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

    public static function canEdit(Model $record): bool
    {
        // Hanya Admin yang bisa mengedit
        return auth()->user()->hasRole(['admin', 'kasir']);
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
                Forms\Components\TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('nama_pelanggan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telepon_pelanggan')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat_pelanggan')
                    ->required()
                    ->columnSpanFull(),
                Select::make('metode_pembayaran')
                    ->options([
                        'transfer' => 'Transfer',
                        'cod' => 'Cash on Delivery',
                        'e_wallet' => 'E-Wallet',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('bukti_pembayaran')
                    ->imageEditor()
                    ->required()
                    ->directory('bukti_pembayaran'),
                Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('menunggu')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn(Pesanan $record) => $record->user?->name ?? 'Pelanggan Tanpa Login'),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('metode_pembayaran'),
                Tables\Columns\TextColumn::make('bukti_pembayaran')
                    ->getStateUsing(fn(Pesanan $record) => $record->bukti_pembayaran ? 'Tersedia' : 'Tidak Tersedia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            DetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
