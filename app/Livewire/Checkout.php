<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Session;

class Checkout extends Component
{
    use WithFileUploads;

    public $nama, $telepon, $alamat, $metode_pembayaran = 'cod';
    public $bukti_pembayaran;

    protected function rules()
    {
        $rules = [
            'nama' => 'required',
            'telepon' => 'required',
            'alamat' => 'required',
            'metode_pembayaran' => 'required|in:cod,transfer_bank,e_wallet',
        ];

        if (in_array($this->metode_pembayaran, ['transfer_bank', 'e_wallet'])) {
            $rules['bukti_pembayaran'] = 'required|image|max:2048'; // 2MB max
        }

        return $rules;
    }

    // public function simpanPesanan()
    // {
    //     $this->validate();

    //     $keranjang = session()->get('keranjang', []);
    //     if (empty($keranjang)) {
    //         session()->flash('error', 'Keranjang kosong!');
    //         return;
    //     }

    //     $pesanan = Pesanan::create([
    //         'user_id' => Auth::check() ? Auth::id() : null,
    //         'nama_pelanggan' => $this->nama,
    //         'telepon_pelanggan' => $this->telepon,
    //         'alamat_pelanggan' => $this->alamat,
    //         'metode_pembayaran' => $this->metode_pembayaran,
    //         'status' => 'menunggu',
    //     ]);

    //     foreach ($keranjang as $item) {
    //         DetailPesanan::create([
    //             'pesanan_id' => $pesanan->id,
    //             'menu_id' => $item['id'],
    //             'kuantitas' => $item['qty'],
    //             'harga' => $item['harga'],
    //         ]);
    //     }

    //     session()->forget('keranjang');
    //     session()->flash('success', 'Pesanan berhasil dibuat!');

    //     return redirect()->route('pesanan');
    // }

    public function simpanPesanan()
    {
        $this->validate();

        $keranjang = session()->get('keranjang', []);
        if (empty($keranjang)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        // Simpan bukti pembayaran jika ada
        $path = null;
        if (in_array($this->metode_pembayaran, ['transfer_bank', 'e_wallet']) && $this->bukti_pembayaran) {
            $path = $this->bukti_pembayaran->store('bukti_pembayaran', 'public');
        }

        $pesanan = Pesanan::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'nama_pelanggan' => $this->nama,
            'telepon_pelanggan' => $this->telepon,
            'alamat_pelanggan' => $this->alamat,
            'metode_pembayaran' => $this->metode_pembayaran,
            'status' => 'menunggu',
            'bukti_pembayaran' => $path,
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
