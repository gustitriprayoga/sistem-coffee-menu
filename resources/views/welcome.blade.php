<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sederhana Coffee Shop</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f8f8;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #a8a8a8;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
    </style>
</head>

<body class="antialiased">
    <div class="relative min-h-screen bg-white">
        <header class="bg-amber-800 text-white p-6 shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-extrabold tracking-tight">Sederhana Coffee Shop</h1>
                <nav>
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

        <section id="menu-section" class="py-12 bg-gray-50">
            <div class="container mx-auto px-4 mb-8 text-center">
                <h3 class="text-2xl font-semibold text-gray-700 mb-4">Kategori Menu Kami </h3>
                {{-- <button wire:click="$dispatch('filterByCategory', { categoryId: null })"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-full mr-2 mb-2 transition duration-300 ease-in-out">
                    Semua
                </button> --}}
                @foreach (\App\Models\KategoriMenu::all() as $kategori)
                    <button wire:click="$dispatch('filterByCategory', { categoryId: {{ $kategori->id }} })"
                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-full mr-2 mb-2 transition duration-300 ease-in-out">
                        {{ $kategori->nama }}
                    </button>
                @endforeach
            </div>
            @livewire('menu-display')
        </section>

        <section class="py-8 bg-gray-100">
            <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Temukan Kami</h3>
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Alamat:</span> Jl. Letnan Boyak Ujung
                    </p>
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Telepon:</span> +62 823-8681-7911
                    </p>
                    <p class="text-gray-700 mb-2">
                        <span class="font-semibold">Email:</span> info@sederhanacoffee.com
                    </p>
                    <p class="text-gray-700">
                        <span class="font-semibold">Contact Person:</span> John Doe (0812-3456-7890)
                    </p>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Lokasi Kami</h3>
                    <div class="aspect-w-16 aspect-h-9 w-full rounded-lg shadow-md overflow-hidden">
                        {{-- Ganti src iframe dengan link Google Maps Anda --}}
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d838.7418931267581!2d101.01565031554867!3d0.33207764059245115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d515dcfda8455b%3A0x5a507734f06b4837!2sKedai%20Kopi%20%22SEDERHANA%22!5e0!3m2!1sen!2sid!4v1751890510089!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Sederhana Coffee Shop. Semua Hak Dilindungi.</p>
                <p>Didesain dengan ❤️ oleh Nama Anda</p>
            </div>
        </footer>
    </div>

    @livewire('cart')

    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PERBAIKAN: Gunakan document.querySelectorAll untuk memilih semua elemen dan memungkinkan forEach
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>

</html>
