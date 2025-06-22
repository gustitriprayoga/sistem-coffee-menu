<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sederhana Coffee Shop</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/filament/support/support.css') }}" />

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

<body>
    {{-- Render komponen WelcomePage Livewire sebagai konten utama --}}
    @livewire('welcome-page')

    {{-- Render komponen Cart Livewire di luar WelcomePage --}}
    {{-- Ini penting agar Cart bisa muncul sebagai floating element --}}
    @livewire('cart')

    @livewireScripts

    <script src="{{ asset('js/filament/filament/app.js') }}"></script>
    <script src="{{ asset('js/filament/notifications/notifications.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
