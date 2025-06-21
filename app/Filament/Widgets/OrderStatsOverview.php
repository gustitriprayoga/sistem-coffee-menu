<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan; // Import model Pesanan
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung total pesanan
        $totalOrders = Pesanan::count();

        // Hitung pesanan berdasarkan status
        $pendingOrders = Pesanan::where('status', 'menunggu')->count();
        $processingOrders = Pesanan::where('status', 'diproses')->count();
        $completedOrders = Pesanan::where('status', 'selesai')->count();
        $cancelledOrders = Pesanan::where('status', 'dibatalkan')->count();

        return [
            Stat::make('Total Pesanan', $totalOrders)
                ->description('Total keseluruhan pesanan')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'), // Warna biru
            Stat::make('Pesanan Menunggu', $pendingOrders)
                ->description('Pesanan yang perlu dikonfirmasi')
                ->descriptionIcon('heroicon-m-arrow-long-right')
                ->color('warning'), // Warna kuning/orange
            Stat::make('Pesanan Diproses', $processingOrders)
                ->description('Pesanan yang sedang disiapkan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'), // Warna default/utama
            Stat::make('Pesanan Selesai', $completedOrders)
                ->description('Pesanan yang sudah diselesaikan')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'), // Warna hijau
            Stat::make('Pesanan Dibatalkan', $cancelledOrders)
                ->description('Pesanan yang dibatalkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'), // Warna merah
        ];
    }
}
