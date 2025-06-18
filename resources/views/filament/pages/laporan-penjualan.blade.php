<x-filament-panels::page>
    <x-filament::card>
        {{ $this->form }}
    </x-filament::card>

    <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
        <x-filament::card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Total Penjualan</h3>
            <p class="text-3xl font-extrabold text-primary-600 mt-2">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
            </p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Jumlah Pesanan</h3>
            <p class="text-3xl font-extrabold text-primary-600 mt-2">{{ $jumlahPesanan }} Pesanan</p>
        </x-filament::card>

        <x-filament::card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Total Keuntungan</h3>
            <p class="text-3xl font-extrabold text-primary-600 mt-2">Rp
                {{ number_format($totalKeuntungan, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Asumsi harga beli tersedia</p>
        </x-filament::card>
    </div>

    @if (!empty($laporanPeriodik))
        <x-filament::card class="mt-8">
            <h3 class="text-xl font-bold mb-4">Laporan Penjualan {{ ucfirst($filterType) }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Periode
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Penjualan
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($laporanPeriodik as $data)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $data['period'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    Rp {{ number_format($data['sales'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
                                    Tidak ada data penjualan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    @endif

    @if (!empty($detailPenjualanPerMenu))
        <x-filament::card class="mt-8">
            <h3 class="text-xl font-bold mb-4">Detail Penjualan Per Menu</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Menu
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jumlah Terjual
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Total Harga
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($detailPenjualanPerMenu as $item)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item['menu_name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    {{ $item['total_quantity'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center dark:text-gray-400">
                                    Tidak ada detail penjualan per menu untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    @endif

</x-filament-panels::page>
