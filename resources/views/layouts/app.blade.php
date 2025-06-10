<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Kopi Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-warning px-4">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            ☕ Kedai Kopi Sederhana
        </a>
        <div class="ms-auto">
            <a href="{{ route('keranjang') }}" class="btn btn-outline-dark me-2">
                🛒 Keranjang
            </a>
            @auth
                <a href="#" class="btn btn-outline-dark me-2">📋 Pesanan</a>
                <form method="POST" action="#" class="d-inline">@csrf
                    <button class="btn btn-dark">Keluar</button>
                </form>
            @else
                <a href="#" class="btn btn-dark">Masuk</a>
            @endauth
        </div>
    </nav>

    <div class="container py-4">
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>

</html>
