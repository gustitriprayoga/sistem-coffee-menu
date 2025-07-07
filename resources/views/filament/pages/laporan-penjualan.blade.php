<x-filament-panels::page>
    {{-- Form Filter Tanggal dan Jenis Laporan --}}
    {{ $this->form }}

    {{-- Tombol Generate Manual (opsional) --}}
    <div class="mt-4">
        <x-filament::button wire:click="generateReport">
            Generate Laporan
        </x-filament::button>
    </div>

    {{-- Statistik Utama --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::card class="col-span-1">
            <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Penjualan</h4>
            <p class="text-3xl font-bold text-amber-600">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        </x-filament::card>
        <x-filament::card class="col-span-1">
            <h4 class="text-lg font-semibold text-gray-700 mb-2">Jumlah Pesanan</h4>
            <p class="text-3xl font-bold text-blue-600">{{ $jumlahPesanan }}</p>
        </x-filament::card>
        <x-filament::card class="col-span-1">
            <h4 class="text-lg font-semibold text-gray-700 mb-2">Total Keuntungan</h4>
            <p class="text-3xl font-bold text-green-600">Rp{{ number_format($totalKeuntungan, 0, ',', '.') }}</p>
        </x-filament::card>
    </div>

    {{-- Detail Penjualan Per Menu --}}
    <x-filament::card class="mt-8 bg-gray-900 text-dark w-full max-w-full">
    <h3 class="text-xl font-semibold mb-4">Detail Penjualan Per Menu</h3>

    @if (!empty($detailPenjualanPerMenu))
        <div class="overflow-x-auto w-full">
            <table class="w-full table-auto text-sm text-black">
                <thead class="bg-gray-800 text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium">Menu</th>
                        <th class="px-6 py-3 text-left font-medium">Total Terjual (Qty)</th>
                        <th class="px-6 py-3 text-left font-medium">Total Harga Jual</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 divide-y divide-gray-700">
                    @foreach ($detailPenjualanPerMenu as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['menu_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $item['total_quantity'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp{{ number_format($item['total_price'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-400 text-center py-4">Tidak ada detail penjualan menu untuk periode ini.</p>
    @endif
</x-filament::card>



    {{-- Laporan Penjualan Periodik --}}
    {{-- <x-filament::card class="mt-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Laporan Penjualan Periodik ({{ ucfirst($filterType) }})
        </h3>
        @if (!empty($laporanPeriodik))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode</th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($laporanPeriodik as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item['period'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp{{ number_format($item['sales'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-4">Tidak ada data penjualan periodik untuk periode ini.</p>
        @endif
    </x-filament::card> --}}
</x-filament-panels::page>
