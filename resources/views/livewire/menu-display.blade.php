<div>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-12 tracking-wide">Menu Lezat Kami</h2>

        {{-- BAGIAN SEARCH FILTER DAN KATEGORI LINGKARAN SUDAH DIPINDAHKAN KE WELCOME-PAGE.BLADE.PHP --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($filteredMenus as $menu)
                <div wire:click="openProductModal({{ $menu->id }})"
                    class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out relative cursor-pointer">
                    @if ($menu->gambar && file_exists(public_path('storage/' . $menu->gambar)))
                        <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}"
                            class="w-full h-48 object-cover">
                    @else
                        <img src="{{ asset('gambar/gambar-default.jpg') }}" alt="{{ $menu->nama }}"
                            class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $menu->nama }}</h4>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($menu->deskripsi, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <span
                                class="text-2xl font-bold text-amber-800">Rp{{ number_format($menu->harga, 0, ',', '.') }}</span>
                            @if ($menu->stock > 0)
                                <span class="text-sm font-semibold text-gray-600">Stok: {{ $menu->stock }}</span>
                            @else
                                <span class="text-sm font-semibold text-red-600">Stok Habis!</span>
                            @endif
                            <button
                                wire:click.stop="addToCart({{ $menu->id }}, '{{ $menu->nama }}', {{ (float) $menu->harga }}, 1)"
                                class="bg-green-600 text-white px-5 py-2 rounded-full hover:bg-green-700 transition duration-300 ease-in-out flex items-center space-x-2"
                                {{ $menu->stock <= 0 ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-600 text-lg">Tidak ada item menu ditemukan yang sesuai
                    dengan pencarian Anda atau di kategori ini.</p>
            @endforelse
        </div>
    </div>

    {{-- Struktur Modal Detail Produk --}}
    @if ($showProductModal && $selectedMenu)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
            x-data="{ show: @entangle('showProductModal') }" x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click.away="show = false; $wire.closeProductModal()">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl transform transition-all overflow-hidden"
                @click.stop>
                <button wire:click="closeProductModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/2 flex justify-center items-center">
                        @if ($selectedMenu->gambar && file_exists(public_path('storage/' . $selectedMenu->gambar)))
                            <img src="{{ asset('storage/' . $selectedMenu->gambar) }}" alt="{{ $selectedMenu->nama }}"
                                class="max-h-80 w-auto rounded-lg shadow-md object-cover">
                        @else
                            <img src="{{ asset('gambar/gambar-default.jpg') }}" alt="{{ $selectedMenu->nama }}"
                                class="max-h-80 w-auto rounded-lg shadow-md object-cover">
                        @endif
                    </div>
                    <div class="md:w-1/2">
                        <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $selectedMenu->nama }}</h3>
                        <p class="text-2xl font-bold text-amber-800 mb-4">
                            Rp{{ number_format($selectedMenu->harga, 0, ',', '.') }}</p>
                        <p class="text-gray-700 text-base mb-6">{{ $selectedMenu->deskripsi }}</p>

                        <div class="flex items-center space-x-4 mb-6">
                            <label class="text-lg font-semibold text-gray-700">Kuantitas:</label>
                            <div class="flex items-center border border-gray-300 rounded-md">
                                <button wire:click="decrementQuantity"
                                    class="px-3 py-1 text-lg font-bold text-amber-700 hover:bg-gray-100 rounded-l-md"
                                    {{ $modalQuantity <= 1 ? 'disabled' : '' }}>-</button>
                                <span class="px-4 py-1 text-lg font-semibold text-gray-800">{{ $modalQuantity }}</span>
                                <button wire:click="incrementQuantity"
                                    class="px-3 py-1 text-lg font-bold text-amber-700 hover:bg-gray-100 rounded-r-md"
                                    {{ $modalQuantity >= $selectedMenu->stock ? 'disabled' : '' }}>+</button>
                            </div>
                        </div>
                        @if ($selectedMenu->stock > 0)
                            <p class="text-sm font-semibold text-gray-600 mb-4">Stok Tersedia:
                                {{ $selectedMenu->stock }}</p>
                        @else
                            <p class="text-sm font-semibold text-red-600 mb-4">Stok Habis!</p>
                        @endif
                        <button
                            wire:click="addToCart({{ $selectedMenu->id }}, '{{ $selectedMenu->nama }}', {{ (float) $selectedMenu->harga }}, {{ $modalQuantity }})"
                            class="w-full bg-green-600 text-white px-6 py-3 rounded-md font-semibold text-lg hover:bg-green-700 transition duration-300 ease-in-out flex items-center justify-center space-x-2"
                            {{ $selectedMenu->stock <= 0 ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Tambah ke Keranjang</span>
                        </button>

                        <p class="text-center text-sm text-gray-500 mt-4">Pilihan Lainnya akan segera hadir!</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
