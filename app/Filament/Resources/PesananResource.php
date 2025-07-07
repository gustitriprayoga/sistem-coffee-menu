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
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormsAction; // Alias untuk Forms Action
use Filament\Forms\Components\Grid; // Import Grid component
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use App\Models\Menu;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

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
                            ->readOnly(fn (string $operation): bool => $operation === 'edit' || $operation === 'view')
                            ->placeholder('Nama lengkap pelanggan'),
                        TextInput::make('telepon_pelanggan')
                            ->required()
                            ->maxLength(20)
                            ->tel()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit' || $operation === 'view')
                            ->placeholder('Nomor telepon pelanggan'),
                        Forms\Components\Textarea::make('alamat_pelanggan')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull()
                            ->readOnly(fn (string $operation): bool => $operation === 'edit' || $operation === 'view')
                            ->placeholder('Alamat pengiriman pelanggan'),
                    ])->columns(2),

                Forms\Components\Section::make('Item Pesanan')
                    ->schema([
                        Repeater::make('details')
                            ->relationship()
                            ->live() // Repeater ini reaktif
                            // PANGGIL updateTotalHarga HANYA DI SINI PADA REPEATER INDUK
                            ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) { // Saat form dimuat
                                self::updateTotalHarga($get, $set);
                            })
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) { // Saat ada perubahan di Repeater
                                self::updateTotalHarga($get, $set);
                            })
                            ->schema([
                                Select::make('menu_id')
                                    ->label('Menu')
                                    ->relationship('menu', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(2)
                                    ->live() // Tetap live untuk update harga
                                    // HAPUS PANGGILAN self::updateTotalHarga DARI SINI
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $menu = \App\Models\Menu::find($state);
                                        if ($menu) {
                                            $set('harga', $menu->harga);
                                        }
                                        // self::updateTotalHarga($get, $set); // BARIS INI DIHAPUS
                                    }),
                                TextInput::make('kuantitas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->default(1)
                                    ->live() // Tetap live untuk update
                                    ->columnSpan(1),
                                    // HAPUS PANGGILAN self::updateTotalHarga DARI SINI
                                    // ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                    //     self::updateTotalHarga($get, $set); // BARIS INI DIHAPUS
                                    // }),
                                TextInput::make('harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->readOnly()
                                    ->columnSpan(1),
                            ])
                            ->defaultItems(1)
                            ->columns(4)
                            ->reorderableWithButtons()
                            ->collapsible()
                            // Panggil updateTotalHarga di sini SETELAH DELETE ITEM
                            ->deleteAction(
                                fn (FormsAction $action) => $action->after(function (Forms\Get $get, Forms\Set $set) {
                                    self::updateTotalHarga($get, $set);
                                }),
                            ),
                    ])->columnSpanFull(),

                Forms\Components\Section::make('Ringkasan Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('metode_pembayaran')
                                    ->options([
                                        'cod' => 'Cash On Delivery',
                                        'transfer_bank' => 'Transfer Bank',
                                        'e_wallet' => 'E-Wallet',
                                        'bayar_di_tempat' => 'Bayar di Tempat',
                                    ])
                                    ->default('bayar_di_tempat')
                                    ->required()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit' || $operation === 'view')
                                    ->columnSpan(1),
                                TextInput::make('total_harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                                // Tombol refresh akan ditambahkan di getFormActions()
                            ]),

                        Select::make('status')
                            ->options([
                                'menunggu' => 'Menunggu Konfirmasi',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->required()
                            ->native(false)
                            ->default('selesai')
                            ->disabled(fn (string $operation): bool => $operation === 'view'),
                        FileUpload::make('bukti_pembayaran')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->disk('public')
                            ->directory('bukti_pembayaran')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->disabled(fn (?Pesanan $record) => $record && ! is_null($record->bukti_pembayaran))
                            ->helperText('Unggah bukti transfer atau e-wallet jika metode pembayaran bukan COD.')
                            ->visible(fn (Forms\Get $get) => in_array($get('metode_pembayaran'), ['transfer_bank', 'e_wallet']))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function updateTotalHarga(Forms\Get $get, Forms\Set $set): void
    {
        $details = $get('details');
        $calculatedTotal = 0;
        if (is_array($details)) {
            foreach ($details as $item) {
                $kuantitas = (float) ($item['kuantitas'] ?? 0);
                $harga = (float) ($item['harga'] ?? 0);
                $calculatedTotal += ($kuantitas * $harga);
            }
        }
        $set('total_harga', $calculatedTotal);
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
                        'bayar_di_tempat' => 'success',
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
                    ])
                    ->default('menunggu'),
                SelectFilter::make('metode_pembayaran')
                    ->options([
                        'cod' => 'Cash On Delivery',
                        'transfer_bank' => 'Transfer Bank',
                        'e_wallet' => 'E-Wallet',
                        'bayar_di_tempat' => 'Bayar di Tempat',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('markAsProcessed')
                    ->label('Proses Pesanan')
                    ->icon('heroicon-o-check-circle')
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
                    ->icon('heroicon-o-check-badge')
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
                    ->icon('heroicon-o-x-circle')
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

    // --- DAFTAR AKSI FORM (TOMBOL DI BAGIAN BAWAH FORM) ---
    // Tambahkan metode ini untuk tombol Refresh Total
    public static function getFormActions(): array // <-- HARUS STATIC DI RESOURCE
    {
        return [
            FormsAction::make('refresh_total_form_action')
                ->label('Refresh Total')
                ->icon('heroicon-m-arrow-path')
                ->color('info')
                ->action(function (Forms\Get $get, Forms\Set $set) {
                    self::updateTotalHarga($get, $set);
                    Notification::make()
                        ->title('Total harga diperbarui secara manual!')
                        ->success()
                        ->send();
                }),
            // Aksi Save dan Cancel bawaan Filament
            Forms\Actions\SaveAction::make(),
            Forms\Actions\CancelAction::make(),
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
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    public static function canView( $record): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    public static function canEdit( $record): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'kasir']);
    }

    public static function canDelete( $record): bool
    {
        return auth()->user()->hasAnyRole(['admin']);
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->hasAnyRole(['admin']);
    }
}
