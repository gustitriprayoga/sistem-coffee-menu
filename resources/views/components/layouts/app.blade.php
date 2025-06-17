<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kedai Kopi Sederhana' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen bg-white">
        <nav class="bg-black p-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center">
                <a href="/" class="text-yellow-400 text-2xl font-bold">Coffee Shop</a>
                <div class="flex items-center space-x-4">
                    <a href="/pesanan-saya" class="text-white hover:text-yellow-400 transition duration-300">Pesanan Saya</a>
                    @livewire('keranjang')
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts /> {{-- PASTIKAN BARIS INI ADA DI SINI --}}
</body>
</html>
