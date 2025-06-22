<div>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-4xl font-bold text-center text-gray-800 mb-12 tracking-wide">Menu Lezat Kami</h2>

        @forelse ($kategoriMenus as $kategori)
            <section class="mb-16">
                <h3 class="text-3xl font-bold text-center text-amber-700 mb-8 pb-4 border-b-2 border-amber-300">
                    {{ $kategori->nama }} {{-- UBAH: Mengakses kolom 'nama' dari KategoriMenu --}}
                </h3>

                @if ($kategori->menus->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($kategori->menus as $menu)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 ease-in-out relative">
                                @if($menu->gambar && file_exists(public_path('storage/' . $menu->gambar))) {{-- UBAH: Memeriksa keberadaan file gambar --}}
                                    <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="w-full h-48 object-cover">
                                @else
                                    <img src="{{ asset('gambar/gambar-default.jpg') }}" alt="{{ $menu->nama }}" class="w-full h-48 object-cover">
                                @endif
                                <div class="p-6">
                                    <h4 class="text-xl font-semibold text-gray-800 mb-2">{{ $menu->nama }}</h4> {{-- UBAH: Mengakses kolom 'nama' dari Menu --}}
                                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($menu->deskripsi, 100) }}</p> {{-- UBAH: Mengakses kolom 'deskripsi' dari Menu --}}
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-bold text-amber-800">Rp{{ number_format($menu->harga, 0, ',', '.') }}</span> {{-- SUDAH BENAR: Mengakses kolom 'harga' --}}
                                        <button
                                            wire:click="addToCart({{ $menu->id }}, '{{ $menu->nama }}', {{ (float) $menu->harga }})" {{-- UBAH: Mengirim 'nama' dan 'harga' --}}
                                            class="bg-green-600 text-white px-5 py-2 rounded-full hover:bg-green-700 transition duration-300 ease-in-out flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Tambah</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-600 text-lg">Tidak ada item menu dalam kategori ini.</p>
                @endif
            </section>
        @empty
            <p class="text-center text-gray-600 text-xl">Belum ada kategori menu yang tersedia. Pastikan Anda telah menjalankan seeder!</p>
        @endforelse
    </div>
</div>
