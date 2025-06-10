<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div class="container mt-4">
        <h2>Riwayat Pesanan</h2>

        @if (count($daftarPesanan))
            @foreach ($daftarPesanan as $pesanan)
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>#{{ $pesanan->id }}</strong> - {{ ucfirst($pesanan->status) }} <br>
                        {{ $pesanan->created_at->format('d M Y H:i') }}
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($pesanan->detailPesanan as $item)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $item->menu->nama }} (x{{ $item->kuantitas }})
                                <span>Rp{{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-footer">
                        <strong>Total:</strong>
                        Rp{{ number_format($pesanan->detailPesanan->sum(fn($d) => $d->harga * $d->kuantitas), 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        @else
            <p>Belum ada pesanan.</p>
        @endif
    </div>

</div>
