<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Pesanan;

class PesananSaya extends Component
{
    public $pesanan;

    public function mount()
    {
        if (Auth::check()) {
            // Untuk pelanggan login
            $this->pesanan = Pesanan::with('detailPesanan.menu')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        } else {
            // Untuk pelanggan tidak login: hanya tampilkan 1 pesanan terakhir
            $this->pesanan = Pesanan::with('detailPesanan.menu')
                ->whereNull('user_id')
                ->latest()
                ->take(1)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.pesanan-saya')->layout('layouts.app');
    }
}
