<div>
    <div class="fixed bottom-4 right-4 z-50">
        <button wire:click="toggleCart"
            class="bg-amber-600 text-white p-4 rounded-full shadow-lg hover:bg-amber-700 transition duration-300 ease-in-out relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M3 1a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V2a1 1 0 00-1-1H3zm0 2h14v10H3V3zM6 15a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" />
            </svg>
            @if (count($cart) > 0)
                <span
                    class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ array_sum(array_column($cart, 'quantity')) }}</span>
            @endif
        </button>
    </div>

    <div
        class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-2xl z-40 transform transition-transform duration-300 ease-in-out
        {{ $showCart ? 'translate-x-0' : 'translate-x-full' }}">
        <div class="p-6 h-full flex flex-col">
            <div class="flex justify-between items-center mb-6 border-b pb-4 flex-shrink-0">
                <h2 class="text-3xl font-bold text-gray-800">Pesanan Anda</h2>
                <button wire:click="toggleCart" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-grow overflow-y-auto pr-2 custom-scrollbar">
                @if (count($cart) > 0)
                    {{-- Daftar Item Keranjang --}}
                    <div> {{-- Div ini membungkus daftar item agar bisa di-scroll bersama form --}}
                        @foreach ($cart as $id => $item)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                <div class="flex-grow">
                                    <h4 class="font-semibold text-gray-800">{{ $item['name'] }}</h4>
                                    <p class="text-gray-600 text-sm">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                        class="text-amber-600 hover:text-amber-800 focus:outline-none focus:ring focus:ring-amber-200 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <span class="font-medium text-gray-700 w-6 text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                        class="text-amber-600 hover:text-amber-800 focus:outline-none focus:ring focus:ring-amber-200 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <button wire:click="removeFromCart({{ $id }})"
                                        class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div> {{-- Akhir div pembungkus daftar item --}}

                    {{-- Ringkasan Total dan Form Pemesanan (Sekarang berada di dalam area scrollable) --}}
                    <div class="mt-6 pt-4 border-t-2 border-gray-200">
                        <div class="flex justify-between items-center text-2xl font-bold text-gray-900 mb-4">
                            <span>Total:</span>
                            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <form wire:submit.prevent="submitOrder">
                            <div class="mb-4">
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama
                                    Pelanggan</label>
                                <input type="text" id="nama_pelanggan" wire:model.live="nama_pelanggan"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500 @error('nama_pelanggan') border-red-500 @enderror"
                                    placeholder="Masukkan nama Anda">
                                @error('nama_pelanggan')
                                    <span class="text-white bg-red-800 p-2 block mt-1 font-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="telepon_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nomor
                                    Telepon</label>
                                <input type="text" id="telepon_pelanggan" wire:model.live="telepon_pelanggan"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500 @error('telepon_pelanggan') border-red-500 @enderror"
                                    placeholder="Cth: 081234567890">
                                @error('telepon_pelanggan')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="alamat_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Alamat
                                    Pengiriman</label>
                                <textarea id="alamat_pelanggan" wire:model.live="alamat_pelanggan" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500 @error('alamat_pelanggan') border-red-500 @enderror"
                                    placeholder="Alamat lengkap Anda"></textarea>
                                @error('alamat_pelanggan')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700 mb-1">Metode
                                    Pembayaran</label>
                                <select id="metode_pembayaran" wire:model.live="metode_pembayaran"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-amber-500 focus:ring-amber-500 @error('metode_pembayaran') border-red-500 @enderror">
                                    <option value="cod">Cash On Delivery (COD)</option>
                                    <option value="bayar_di_tempat">Bayar Di Tempat</option>
                                    <option value="transfer_bank">Transfer Bank</option>
                                    <option value="e_wallet">E-Wallet</option>
                                </select>
                                @error('metode_pembayaran')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- --- BLOK KONDISIONAL UNTUK INFO PEMBAYARAN DAN UPLOAD FILE --- --}}
                            @if (in_array($metode_pembayaran, ['transfer_bank', 'e_wallet']))
                                <div class="mb-4">
                                    <label for="bukti_pembayaran_file" class="block text-sm font-medium text-gray-700 mb-1">
                                        Unggah Bukti Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" id="bukti_pembayaran_file" wire:model="buktiPembayaranFile"
                                        class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100 @error('buktiPembayaranFile') border-red-500 @enderror">

                                    @error('buktiPembayaranFile')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror

                                    <div wire:loading wire:target="buktiPembayaranFile" class="text-green-600 text-sm mt-2">
                                        Mengunggah bukti pembayaran...
                                    </div>

                                    @if ($buktiPembayaranFile && method_exists($buktiPembayaranFile, 'temporaryUrl'))
                                        <img src="{{ $buktiPembayaranFile->temporaryUrl() }}" class="mt-4 max-w-full h-auto rounded-md shadow-md">
                                    @endif
                                </div>
                            @endif

                            @if ($metode_pembayaran === 'transfer_bank')
                                <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-4 mb-6 rounded-md" role="alert">
                                    <p class="font-bold">Info Transfer Bank:</p>
                                    <p>Bank: {{ $bankTransferInfo['nama_bank'] }}</p>
                                    <p>Nomor Rekening: <span class="font-semibold text-lg">{{ $bankTransferInfo['nomor_rekening'] }}</span></p>
                                    <p>Atas Nama: {{ $bankTransferInfo['nama_pemilik'] }}</p>
                                    <p class="mt-2 text-sm">Mohon lakukan transfer sejumlah total pesanan dan unggah bukti pembayaran di atas.</p>
                                </div>
                            @elseif ($metode_pembayaran === 'e_wallet')
                                <div class="bg-purple-50 border-l-4 border-purple-400 text-purple-800 p-4 mb-6 rounded-md" role="alert">
                                    <p class="font-bold">Info Pembayaran E-Wallet:</p>
                                    <p>Platform: {{ $eWalletInfo['nama_ewallet'] }}</p>
                                    <p>Nomor HP: <span class="font-semibold text-lg">{{ $eWalletInfo['nomor_hp_ewallet'] }}</span></p>
                                    <p>Atas Nama: {{ $eWalletInfo['nama_pemilik'] }}</p>
                                    <p class="mt-2 text-sm">Mohon lakukan pembayaran sejumlah total pesanan ke nomor ini dan unggah bukti pembayaran di atas.</p>
                                </div>
                            @endif
                            {{-- --- AKHIR BLOK KONDISIONAL --- --}}

                            <button type="submit"
                                class="w-full bg-green-600 text-white py-3 rounded-md font-semibold text-lg hover:bg-green-700 transition duration-300 ease-in-out">
                                Pesan Sekarang
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-grow flex items-center justify-center text-center text-gray-500">
                        <p>Keranjang Anda kosong. Yuk, pilih menu favoritmu!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 ease-in-out {{ $showCart ? 'opacity-100 visible' : 'opacity-0 hidden' }}"
        wire:click="toggleCart">
    </div>
</div>
