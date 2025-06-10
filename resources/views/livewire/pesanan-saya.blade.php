<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="container mt-4">
        <h2 class="mb-4">Riwayat Pesanan Anda</h2>

        @forelse ($pesanan as $p)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Pesanan #{{ $p->id }}</strong> - Status: <span
                        class="badge bg-warning text-dark">{{ ucfirst($p->status) }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> {{ $p->nama_pelanggan }}</p>
                    <p><strong>No. Telepon:</strong> {{ $p->telepon_pelanggan }}</p>
                    <p><strong>Alamat:</strong> {{ $p->alamat_pelanggan }}</p>
                    <p><strong>Metode Pembayaran:</strong>
                        {{ strtoupper(str_replace('_', ' ', $p->metode_pembayaran)) }}</p>
                    @if ($p->bukti_pembayaran)
                        <p><strong>Bukti Pembayaran:</strong></p>
                        <img src="{{ Storage::url($p->bukti_pembayaran) }}" class="img-fluid mb-3"
                            style="max-height: 200px;">
                    @endif

                    <h5>Detail Menu:</h5>
                    <ul class="list-group mb-3">
                        @foreach ($p->detailPesanan as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item->menu->nama }} x {{ $item->kuantitas }}
                                <span>Rp{{ number_format($item->harga * $item->kuantitas, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <h5>Total: <strong>
                            Rp{{ number_format($p->detailPesanan->sum(fn($i) => $i->harga * $i->kuantitas), 0, ',', '.') }}
                        </strong></h5>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Belum ada pesanan.</div>
        @endforelse
    </div>


</div>
