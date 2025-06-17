<div class="container mx-auto p-4 md:p-8">
    <div class="bg-yellow-400 text-black p-6 rounded-lg shadow-md mb-8 text-center">
        <h1 class="text-4xl font-extrabold mb-2">Selamat Datang di Coffee Shop Kami!</h1>
        <p class="text-lg">Nikmati kopi terbaik dan hidangan lezat kami.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0 md:space-x-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari menu..."
            class="w-full md:w-1/3 p-3 rounded-lg border-2 border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 transition duration-200 ease-in-out shadow-sm">

        <div class="flex flex-wrap justify-center md:justify-end gap-2 w-full md:w-2/3">
            <button wire:click="filterByKategori(null)"
                class="{{ is_null($selectedKategoriId) ? 'bg-black text-yellow-400' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                Semua
            </button>
            @foreach ($kategoriMenus as $kategori)
                <button wire:click="filterByKategori({{ $kategori->id }})"
                    class="{{ $selectedKategoriId == $kategori->id ? 'bg-black text-yellow-400' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} font-semibold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    {{ $kategori->nama_kategori }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($menus as $menu)
            <div
                class="bg-white rounded-lg shadow-xl overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                <img src="{{ asset('storage/' . $menu->gambar_menu) }}" alt="{{ $menu->nama_menu }}"
                    class="w-full h-48 object-cover">
                <div class="p-5">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $menu->nama_menu }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $menu->deskripsi }}</p>
                    <div class="flex justify-between items-center">
                        <span
                            class="text-yellow-600 font-extrabold text-2xl">Rp{{ number_format($menu->harga, 0, ',', '.') }}</span>
                        <button wire:click="addToCart({{ $menu->id }})"
                            class="bg-black text-yellow-400 hover:bg-yellow-400 hover:text-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 font-semibold py-2 px-4 rounded-full flex items-center transition duration-300 ease-in-out shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.5 14 6.35 14H15a1 1 0 000-2H6.35c-.372 0-.57-.237-.8-.629l-1.385-2.175.721-2.875A.999.999 0 004.86 5H18a1 1 0 100-2H4.859L4.5 1H3zM16 16c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zM6 18c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2z" />
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 text-lg py-10">Tidak ada menu yang ditemukan.</div>
        @endforelse
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('menuAdded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Ditambahkan ke Keranjang!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            });
        });
    </script>
</div>
