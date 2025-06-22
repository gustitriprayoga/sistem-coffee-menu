<div> {{-- INI ADALAH SATU-SATUNYA ROOT ELEMENT UNTUK KOMPONEN LIVEWIRE INI --}}

    <header class="bg-amber-800 text-white p-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-extrabold tracking-tight">Sederhana Coffee Shop</h1>
            <nav class="space-x-4">
                <a href="/" class="hover:text-gray-200">Beranda</a>
                <a href="#menu-section" class="hover:text-gray-200">Menu</a>

            </nav>
        </div>
    </header>

    <section class="relative bg-cover bg-center h-[500px] flex items-center justify-center text-center text-white"
        style="background-image: url('https://images.unsplash.com/photo-1511920170033-0c8a5fa810e8?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 p-8">
            <h2 class="text-5xl md:text-6xl font-bold leading-tight mb-4 animate-fade-in-down">Nikmati, Rasakan, dan
                Santai</h2>
            <p class="text-xl md:text-2xl mb-8 animate-fade-in-up">Rasakan kopi terbaik dan hidangan lezat.</p>
            <a href="#menu-section"
                class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 ease-in-out shadow-lg animate-fade-in-up">
                Lihat Menu Kami
            </a>
        </div>
    </section>

    <section class="py-8 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-center mb-8">
                <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari menu favoritmu..."
                    class="w-full max-w-md px-6 py-3 rounded-full border-2 border-gray-300 focus:outline-none focus:border-amber-500 transition duration-300 shadow-md text-lg">
            </div>

            <div class="flex overflow-x-auto justify-start md:justify-center py-4 space-x-6 custom-scrollbar">
                {{-- Tombol "Semua" Kategori --}}
                <button wire:click="selectCategory('all')"
                    class="flex-shrink-0 flex flex-col items-center p-3 rounded-full transition-all duration-300
                    {{ $selectedCategory === 'all' ? 'bg-amber-700 text-white shadow-lg scale-105' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    style="width: 90px; height: 90px; justify-content: center;">
                    <img src="https://via.placeholder.com/40" alt="Semua" class="w-10 h-10 rounded-full mb-1 object-cover">
                    <span class="text-xs font-semibold text-center">Semua</span>
                </button>

                @foreach ($kategoriMenus as $kategori)
                    <button wire:click="selectCategory({{ $kategori->id }})"
                        class="flex-shrink-0 flex flex-col items-center p-3 rounded-full transition-all duration-300
                        {{ $selectedCategory == $kategori->id ? 'bg-amber-700 text-white shadow-lg scale-105' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        style="width: 90px; height: 90px; justify-content: center;">
                        <img src="https://via.placeholder.com/40?text={{ Str::limit($kategori->nama, 1, '') }}" alt="{{ $kategori->nama }}" class="w-10 h-10 rounded-full mb-1 object-cover">
                        <span class="text-xs font-semibold text-center">{{ Str::limit($kategori->nama, 8) }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section id="menu-section" class="py-12 bg-gray-50">
        {{-- Meneruskan properti filter ke komponen menu-display --}}
        @livewire('menu-display', ['search' => $search, 'selectedCategory' => $selectedCategory])
    </section>

    {{-- @livewire('cart') --}}

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Sederhana Coffee Shop. Semua Hak Dilindungi.</p>
            <p>Didesain dengan ❤️ oleh Nama Anda</p>
        </div>
    </footer>
</div>
