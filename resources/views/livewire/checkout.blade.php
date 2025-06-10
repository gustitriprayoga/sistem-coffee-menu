<div>
    {{-- <div class="container mt-4"> --}}
        <h2>Checkout</h2>

        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="simpanPesanan">
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" wire:model="nama" class="form-control">
                @error('nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>No. Telepon</label>
                <input type="text" wire:model="telepon" class="form-control">
                @error('telepon')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea wire:model="alamat" class="form-control"></textarea>
                @error('alamat')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Metode Pembayaran</label>
                <select wire:model="metode_pembayaran" class="form-select">
                    <option value="cod">COD</option>
                    <option value="transfer_bank">Transfer Bank</option>
                    <option value="e_wallet">E-Wallet</option>
                </select>
                @error('metode_pembayaran')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <h4>Keranjang</h4>
            <ul class="list-group mb-3">
                @forelse ($keranjang as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $item['nama'] }} x {{ $item['qty'] }}</span>
                        <strong>Rp{{ number_format($item['harga'] * $item['qty']) }}</strong>
                    </li>
                @empty
                    <li class="list-group-item">Keranjang kosong</li>
                @endforelse
            </ul>

            <h5>Total: <strong>Rp{{ number_format($total) }}</strong></h5>

            <button class="btn btn-success">Buat Pesanan</button>
        </form>
    {{-- </div> --}}
</div>
