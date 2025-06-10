<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    public $nama, $telepon, $alamat, $metode_pembayaran = 'cod';

    protected $rules = [
        'nama' => 'required',
        'telepon' => 'required',
        'alamat' => 'required',
        'metode_pembayaran' => 'required|in:cod,transfer_bank,e_wallet',
    ];

    public function simpanPesanan()
    {
        $this->validate();

        $keranjang = session()->get('keranjang', []);
        if (empty($keranjang)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $pesanan = Pesanan::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'nama_pelanggan' => $this->nama,
            'telepon_pelanggan' => $this->telepon,
            'alamat_pelanggan' => $this->alamat,
            'metode_pembayaran' => $this->metode_pembayaran,
            'status' => 'menunggu',
        ]);

        foreach ($keranjang as $item) {
            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'menu_id' => $item['id'],
                'kuantitas' => $item['qty'],
                'harga' => $item['harga'],
            ]);
        }

        session()->forget('keranjang');
        session()->flash('success', 'Pesanan berhasil dibuat!');

        return redirect()->route('pesanan');
    }

    public function render()
    {
        $keranjang = session()->get('keranjang', []);
        $total = collect($keranjang)->sum(fn($item) => $item['harga'] * $item['qty']);
        return view('livewire.checkout', compact('keranjang', 'total'))
            ->layout('layouts.app');
    }
}
