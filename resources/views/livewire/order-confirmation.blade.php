<div>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-center text-green-700 mb-6">Pesanan Anda Berhasil Dibuat!</h2>
            <p class="text-center text-gray-600 mb-8">Terima kasih telah memesan di Sederhana Coffee Shop.</p>

            @if ($pesanan)
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">Detail Pesanan #{{ $pesanan->id }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <p><span class="font-medium">Nama Pelanggan:</span> {{ $pesanan->nama_pelanggan }}</p>
                            <p><span class="font-medium">Telepon:</span> {{ $pesanan->telepon_pelanggan }}</p>
                            <p><span class="font-medium">Alamat:</span> {{ $pesanan->alamat_pelanggan }}</p>
                        </div>
                        <div>
                            <p><span class="font-medium">Metode Pembayaran:</span> {{ ucfirst(str_replace('_', ' ', $pesanan->metode_pembayaran)) }}</p>
                            <p><span class="font-medium">Total Harga:</span> Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                            <p><span class="font-medium">Status Pesanan:</span> {{ ucfirst($pesanan->status) }}</p>
                            <p><span class="font-medium">Tanggal Pesanan:</span> {{ $pesanan->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">Item Pesanan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Menu</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Harga Satuan</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Kuantitas</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-700">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detailPesanans as $detail)
                                    <tr class="border-b hover:bg-gray-50">
                                        {{-- Pastikan relasi 'menu' di 'DetailPesanan' bekerja dengan baik --}}
                                        <td class="py-3 px-4">{{ $detail->menu->nama ?? 'N/A' }}</td> {{-- Mengakses nama menu --}}
                                        <td class="py-3 px-4">Rp{{ number_format($detail->harga, 0, ',', '.') }}</td> {{-- Mengakses harga satuan dari detail pesanan --}}
                                        <td class="py-3 px-4">{{ $detail->kuantitas }}</td>
                                        <td class="py-3 px-4">Rp{{ number_format($detail->harga * $detail->kuantitas, 0, ',', '.') }}</td> {{-- Menghitung subtotal --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="/" class="bg-amber-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-amber-700 text-center transition duration-300">
                        Kembali ke Menu Utama
                    </a>
                    {{-- Tombol untuk cetak/download PDF --}}
                    <button
                        onclick="window.print()"
                        class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 text-center transition duration-300 print:hidden">
                        Cetak Bukti Pemesanan
                    </button>
                </div>
            @else
                <p class="text-center text-red-500 text-lg">Detail pesanan tidak dapat dimuat. Silakan coba lagi.</p>
                <div class="flex justify-center mt-6">
                     <a href="/" class="bg-amber-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-amber-700 text-center transition duration-300">
                        Kembali ke Menu Utama
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                left: 0;
                top: 0;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</div>
