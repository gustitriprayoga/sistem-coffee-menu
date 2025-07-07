<?php

namespace App\Filament\Pages;

use App\Models\Pesanan; // Pastikan model Pesanan diimport
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable; // Import trait ini
use Filament\Tables\Contracts\HasTable; // Import interface ini
use Filament\Tables\Table; // Import Table class
use Filament\Tables\Columns\TextColumn; // Import TextColumn
use Filament\Tables\Filters\SelectFilter; // Import SelectFilter
use Filament\Tables\Actions\Action; // Import Action jika perlu aksi di tabel
use App\Filament\Resources\PesananResource; // Untuk link ke View PesananResource
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OrderHistoryPage extends Page implements HasTable // Implementasikan HasTable
{
    use InteractsWithTable; // Gunakan trait ini

    protected static ?string $navigationIcon = 'heroicon-o-clock'; // Icon di sidebar (berbeda dari PesananResource)
    protected static ?string $navigationLabel = 'Riwayat Pembelian'; // Label di sidebar
    protected static ?string $slug = 'riwayat-pembelian'; // URL slug untuk halaman ini
    protected static ?string $navigationGroup = 'Laporan'; // Grup navigasi baru di sidebar
    protected static ?int $navigationSort = 1; // Urutan di dalam grup

    protected static string $view = 'filament.pages.order-history-page';

    // Metode untuk mendefinisikan tabel di halaman kustom
    public function table(Table $table): Table
    {
        return $table
            ->query(Pesanan::query()->orderBy('created_at', 'desc')) // Query semua pesanan, urutkan dari terbaru
            ->columns([
                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable(),
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
                        'bayar_di_tempat' => 'warning',
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
                        'bayar_di_tempat' => 'Bayar di Tempat',
                    ]),
            ])
            ->actions([
                // Aksi untuk melihat detail pesanan menggunakan PesananResource View page
                Action::make('viewOrder')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Pesanan $record): string => PesananResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(), // Buka detail di tab baru
                // Tidak ada aksi untuk mengubah status di halaman ini, karena fokusnya riwayat/report
            ])
            ->bulkActions([
                // Tidak ada bulk actions yang biasanya dibutuhkan untuk halaman riwayat
                ExportBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // --- PENGATURAN IZIN SPATIE UNTUK HALAMAN INI ---
    protected static bool $shouldRegisterNavigation = true; // Agar muncul di sidebar

    public static function canAccess(): bool
    {
        // Hanya user dengan peran 'admin' atau 'kasir' yang bisa mengakses halaman ini
        return auth()->user()->hasAnyRole(['kasir']);
    }

    public function getTitle(): string
    {
        return 'Riwayat Pembelian';
    }
}
