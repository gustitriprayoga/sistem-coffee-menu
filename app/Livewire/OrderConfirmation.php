<?php

namespace App\Livewire;
use Livewire\Component;
use App\Models\Pesanan; // Pastikan path model Pesanan sudah benar
use App\Models\DetailPesanan; // Pastikan path model DetailPesanan sudah benar
use App\Models\Menu; // Pastikan path model Menu sudah benar

class OrderConfirmation extends Component
{
    public $pesananId;
    public $pesanan;
    public $detailPesanans;

    // Use a rule to ensure pesananId is required and exists
    // Ini memastikan bahwa ID pesanan yang diterima valid
    protected $rules = [
        'pesananId' => 'required|exists:pesanans,id',
    ];

    public function mount($pesananId = null)
    {
        $this->pesananId = $pesananId;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        if ($this->pesananId) {
            // Load Pesanan dengan detailnya dan menu yang terkait dengan setiap detail
            // Ini sangat penting agar Anda bisa mengakses nama menu, harga, dll.
            $this->pesanan = Pesanan::with(['detail' => function($query) {
                $query->with('menu'); // Memuat relasi 'menu' di dalam 'details'
            }])->find($this->pesananId);

            if ($this->pesanan) {
                $this->detailPesanans = $this->pesanan->details;
            } else {
                // Jika pesanan tidak ditemukan, arahkan kembali ke halaman utama atau tampilkan error
                session()->flash('error', 'Pesanan tidak ditemukan.');
                return redirect()->to('/');
            }
        } else {
            // Jika tidak ada pesananId yang diberikan, arahkan kembali
            session()->flash('error', 'ID Pesanan tidak valid.');
            return redirect()->to('/');
        }
    }

    public function render()
    {
        return view('livewire.order-confirmation');
    }
}
