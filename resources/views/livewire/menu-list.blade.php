<div>
    <div>
        <div class="text-center mb-4">
            <h2 class="fw-bold">Menu</h2>
            <div class="d-flex justify-content-center mb-3">
                <input type="text" class="form-control w-50" wire:model.debounce.500ms="search"
                    placeholder="Cari menu...">
            </div>

            <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                @foreach ($kategoris as $kategori)
                    <a href="#" class="btn btn-outline-dark">{{ $kategori->nama }}</a>
                @endforeach
            </div>
        </div>

        <h4 class="mb-3">Menu Terbaru</h4>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            @forelse ($menus as $menu)
                <div class="col">
                    <div class="card h-100">
                        <img src="{{ $menu->gambar ?? 'https://via.placeholder.com/150' }}" class="card-img-top"
                            alt="{{ $menu->nama }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $menu->nama }}</h5>
                            <p class="card-text">Rp{{ number_format($menu->harga, 0, ',', '.') }}</p>
                            <button wire:click="$emit('tambahKeranjang', {{ $menu->id }})"
                                class="btn btn-success">Tambah ke Keranjang</button>
                        </div>
                    </div>
                </div>
            @empty
                <p>Tidak ada menu ditemukan.</p>
            @endforelse
        </div>
    </div>
</div>
