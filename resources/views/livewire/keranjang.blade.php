<div>
    <button wire:click="toggleCart" class="relative text-white hover:text-yellow-400 transition duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if (count($cart) > 0)
            <span
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                {{ array_sum(array_column($cart, 'quantity')) }}
            </span>
        @endif
    </button>

    @if ($isCartOpen)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center z-50 p-4">
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto relative transform transition-all duration-300 scale-100 opacity-100">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-3">
                        <h2 class="text-2xl font-bold text-gray-900">Keranjang Belanja</h2>
                        <button wire:click="toggleCart" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if (count($cart) > 0)
                        <div class="max-h-80 overflow-y-auto mb-4 pr-2">
                            @foreach ($cart as $item)
                                <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                                    <div class="flex items-center space-x-3">
                                        @if ($item['gambar_menu'])
                                            <img src="{{ asset('storage/' . $item['gambar_menu']) }}"
                                                alt="{{ $item['nama_menu'] }}"
                                                class="w-16 h-16 object-cover rounded-md">
                                        @else
                                            <div
                                                class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center text-gray-500 text-xs">
                                                No Image</div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $item['nama_menu'] }}</h3>
                                            <p class="text-sm text-gray-600">
                                                Rp{{ number_format($item['harga'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button wire:click="decrementQuantity({{ $item['id'] }})"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-l-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="px-2 text-sm font-medium">{{ $item['quantity'] }}</span>
                                            <button wire:click="incrementQuantity({{ $item['id'] }})"
                                                class="px-2 py-1 text-gray-600 hover:bg-gray-100 rounded-r-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                        <button wire:click="removeFromCart({{ $item['id'] }})"
                                            class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between items-center font-bold text-lg mb-4 border-t pt-4">
                            <span>Total:</span>
                            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col space-y-3">
                            <a href="{{ route('checkout') }}"
                                class="w-full bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg text-center hover:bg-yellow-600 transition duration-300 ease-in-out shadow-lg">
                                Lanjutkan ke Pembayaran
                            </a>
                            <button wire:click="clearCart"
                                class="w-full bg-red-500 text-white font-bold py-3 px-4 rounded-lg text-center hover:bg-red-600 transition duration-300 ease-in-out shadow-lg">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">Keranjang Anda kosong.</p>
                        <div class="flex justify-center">
                            <button wire:click="toggleCart"
                                class="bg-black text-yellow-400 hover:bg-yellow-400 hover:text-black font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-lg">
                                Lanjutkan Belanja
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
