  <div>
      <h4 class="mb-4">Keranjang Anda</h4>

      @if (count($items))
          <ul class="list-group mb-4">
              @foreach ($items as $id => $item)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                      {{ $item['nama'] }} (x{{ $item['qty'] }})
                      <span>Rp{{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</span>
                      <button class="btn btn-sm btn-danger" wire:click="hapus({{ $id }})">Hapus</button>
                  </li>
              @endforeach
          </ul>

          <a href="{{ route('checkout') }}" class="btn btn-primary">Lanjut ke Pembayaran</a>
      @else
          <p>Keranjang kosong.</p>
      @endif
  </div>
