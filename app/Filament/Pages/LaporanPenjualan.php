<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu; // Pastikan ini diimport untuk relasi
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Auth;

class LaporanPenjualan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Penjualan';
    protected static ?string $slug = 'laporan-penjualan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.laporan-penjualan';

    public ?string $filterType = 'daily';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public $totalPenjualan = 0;
    public $jumlahPesanan = 0;
    public $totalKeuntungan = 0;
    public $detailPenjualanPerMenu = [];
    public $laporanPeriodik = [];

    public function mount(): void
    {
        // Set nilai default untuk startDate dan endDate saat pertama kali halaman dimuat
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        $this->updateDateRange(); // Pastikan rentang tanggal diatur berdasarkan filterType default
        $this->generateReport();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Tanggal Mulai')
                        ->default(fn() => Carbon::now()->startOfMonth()->toDateString())
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->generateReport()),
                    DatePicker::make('endDate')
                        ->label('Tanggal Akhir')
                        ->default(fn() => Carbon::now()->endOfMonth()->toDateString())
                        ->reactive()
                        ->afterStateUpdated(fn() => $this->generateReport()),
                    Select::make('filterType')
                        ->label('Tipe Laporan')
                        ->options([
                            'daily' => 'Harian',
                            'weekly' => 'Mingguan',
                            'monthly' => 'Bulanan',
                        ])
                        ->default('daily')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            $this->filterType = $state;
                            $this->updateDateRange();
                            $this->generateReport();
                        }),
                ])
                ->columns(3),
        ];
    }

    protected function updateDateRange(): void
    {
        switch ($this->filterType) {
            case 'daily':
                $this->startDate = Carbon::now()->startOfDay()->toDateString();
                $this->endDate = Carbon::now()->endOfDay()->toDateString();
                break;
            case 'weekly':
                $this->startDate = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();
                $this->endDate = Carbon::now()->endOfWeek(Carbon::SATURDAY)->toDateString();
                break;
            case 'monthly':
                $this->startDate = Carbon::now()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->endOfMonth()->toDateString();
                break;
            default:
                $this->startDate = Carbon::now()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->endOfMonth()->toDateString();
                break;
        }
    }


    public function generateReport(): void
    {
        try {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();

            if ($startDate->greaterThan($endDate)) {
                Notification::make()
                    ->title('Rentang Tanggal Tidak Valid')
                    ->body('Tanggal mulai harus sebelum tanggal akhir.')
                    ->danger()
                    ->send();
                return;
            }

            // --- Bagian Laporan Keuangan (Total Penjualan, Jumlah Pesanan, Keuntungan) ---
            $this->totalPenjualan = Pesanan::whereBetween('created_at', [$startDate, $endDate])->sum('total_harga');
            $this->jumlahPesanan = Pesanan::whereBetween('created_at', [$startDate, $endDate])->count();

            // Total Keuntungan: Mengambil dari total_harga pada detail_pesanans
            // Karena tidak ada harga_beli, 'keuntungan' dianggap sama dengan 'harga jual'
            $this->totalKeuntungan = DetailPesanan::whereHas('pesanan', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
                ->select(DB::raw('SUM(harga * kuantitas) as total_profit'))
                ->first()
                ->total_profit ?? 0;

            // --- Bagian Detail Penjualan Per Menu ---
            $this->detailPenjualanPerMenu = DetailPesanan::with('menu')
                ->whereHas('pesanan', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->select('menu_id', DB::raw('SUM(kuantitas) as total_jumlah'), DB::raw('SUM(harga * kuantitas) as total_harga_menu'))
                ->groupBy('menu_id')
                ->orderByDesc('total_harga_menu')
                ->get()
                ->map(function ($item) {
                    return [
                        'menu_name' => $item->menu->nama ?? 'N/A', // PERBAIKAN DI SINI: Gunakan 'nama'
                        'total_quantity' => $item->total_jumlah,
                        'total_price' => $item->total_harga_menu,
                    ];
                })
                ->toArray();

            // --- Bagian Laporan Periodik (Harian/Mingguan/Bulanan) ---
            $this->laporanPeriodik = [];
            $currentDate = $startDate->copy();

            while ($currentDate->lessThanOrEqualTo($endDate)) {
                $periodStart = null;
                $periodEnd = null;
                $periodLabel = '';
                $breakLoop = false;

                if ($this->filterType === 'daily') {
                    $periodStart = $currentDate->copy()->startOfDay();
                    $periodEnd = $currentDate->copy()->endOfDay();
                    $periodLabel = $currentDate->translatedFormat('d F Y');
                    $currentDate->addDay();
                } elseif ($this->filterType === 'weekly') {
                    $periodStart = $currentDate->copy()->startOfWeek(Carbon::SUNDAY)->startOfDay();
                    $periodEnd = $currentDate->copy()->endOfWeek(Carbon::SATURDAY)->endOfDay();
                    $periodLabel = 'Minggu ' . $currentDate->weekOfYear . ' (' . $periodStart->translatedFormat('d M') . ' - ' . $periodEnd->translatedFormat('d M Y') . ')';
                    $currentDate->addWeek();
                } elseif ($this->filterType === 'monthly') {
                    $periodStart = $currentDate->copy()->startOfMonth()->startOfDay();
                    $periodEnd = $currentDate->copy()->endOfMonth()->endOfDay();
                    $periodLabel = $currentDate->translatedFormat('F Y');
                    $currentDate->addMonth();
                } else {
                    $breakLoop = true;
                }

                if ($breakLoop) break;

                $actualPeriodEnd = $periodEnd->greaterThan($endDate) ? $endDate : $periodEnd;

                $periodSales = Pesanan::whereBetween('created_at', [$periodStart, $actualPeriodEnd])->sum('total_harga');

                if ($periodSales > 0) {
                    $this->laporanPeriodik[] = [
                        'period' => $periodLabel,
                        'sales' => $periodSales,
                    ];
                }

                if ($currentDate->greaterThan($endDate) && ($this->filterType === 'weekly' || $this->filterType === 'monthly')) {
                    break;
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Tidak dapat memuat laporan: ' . $e->getMessage())
                ->danger()
                ->send();
            \Log::error('Error generating report: ' . $e->getMessage());
        }
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function getHeading(): string
    {
        return 'Laporan Penjualan & Keuangan';
    }
}
