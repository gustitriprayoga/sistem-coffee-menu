<?php


namespace App\Filament\Resources;

use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Models\Pesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Manajemen Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        TextInput::make('nama_pelanggan')
                            ->required()
                            ->maxLength(255)
                            ->readOnly(fn (string $operation): bool => $operation === 'edit')
                            ->placeholder('Nama lengkap pelanggan'),
                        TextInput::make('telepon_pelanggan')
                            ->required()
                            ->maxLength(20)
                            ->tel()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit')
                            ->placeholder('Nomor telepon pelanggan'),
                        Forms\Components\Textarea::make('alamat_pelanggan')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit')
                            ->placeholder('Alamat pengiriman pelanggan'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Pesanan')
                    ->schema([
                        Select::make('metode_pembayaran')
                            ->options([
                                'cod' => 'Cash On Delivery',
                                'transfer_bank' => 'Transfer Bank',
                                'e_wallet' => 'E-Wallet',
                            ])
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        TextInput::make('total_harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                        Select::make('status')
                            ->options([
                                'menunggu' => 'Menunggu Konfirmasi',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->required()
                            ->native(false)
                            ->default('menunggu')
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        FileUpload::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->disk('public')
                            ->directory('bukti_pembayaran')
                            ->visibility('private') // Atau 'public' jika ingin bisa diakses langsung
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            // UBAH: readOnly() menjadi disabled(), dan logika dibalik
                            ->disabled(fn ($record) => ! is_null($record->bukti_pembayaran))
                            ->helperText('Unggah bukti transfer atau e-wallet jika metode pembayaran bukan COD.')
                            ->visible(fn (Forms\Get $get) => in_array($get('metode_pembayaran'), ['transfer_bank', 'e_wallet']))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('telepon_pelanggan')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cod' => 'success',
                        'transfer_bank' => 'info',
                        'e_wallet' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->prefix('Rp')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '.', decimalSeparator: ',')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Pesan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'menunggu' => 'Menunggu Konfirmasi',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                SelectFilter::make('metode_pembayaran')
                    ->options([
                        'cod' => 'Cash On Delivery',
                        'transfer_bank' => 'Transfer Bank',
                        'e_wallet' => 'E-Wallet',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('markAsProcessed')
                    ->label('Proses Pesanan')
                    ->icon('eva-car-outline')
                    ->color('primary')
                    ->visible(fn (Pesanan $record): bool => $record->status === 'menunggu')
                    ->action(function (Pesanan $record) {
                        $record->status = 'diproses';
                        $record->save();
                        Notification::make()
                            ->title('Pesanan berhasil diproses.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Proses Pesanan')
                    ->modalDescription('Apakah Anda yakin ingin mengubah status pesanan ini menjadi "Diproses"?')
                    ->modalSubmitActionLabel('Ya, Proses'),
                Action::make('markAsCompleted')
                    ->label('Selesai Pesanan')
                    ->icon('heroicon-s-check-badge')
                    ->color('success')
                    ->visible(fn (Pesanan $record): bool => $record->status === 'diproses')
                    ->action(function (Pesanan $record) {
                        $record->status = 'selesai';
                        $record->save();
                        Notification::make()
                            ->title('Pesanan berhasil diselesaikan.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Selesai Pesanan')
                    ->modalDescription('Apakah Anda yakin ingin mengubah status pesanan ini menjadi "Selesai"?')
                    ->modalSubmitActionLabel('Ya, Selesai'),
                Action::make('markAsCancelled')
                    ->label('Batalkan Pesanan')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (Pesanan $record): bool => $record->status !== 'dibatalkan' && $record->status !== 'selesai')
                    ->action(function (Pesanan $record) {
                        $record->status = 'dibatalkan';
                        $record->save();
                        Notification::make()
                            ->title('Pesanan berhasil dibatalkan.')
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembatalan Pesanan')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Batalkan'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
            'view' => Pages\ViewPesanan::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);
    }

    public static function canView( $record): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);
    }

    public static function canEdit( $record): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);
    }

    public static function canDelete( $record): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);

    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->hasAnyRole([ 'kasir']);
    }
}
