<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Pesanan extends Component
{
    public $daftarPesanan = [];

    public function mount()
    {
        if (Auth::check()) {
            $this->daftarPesanan = Pesanan::with('detailPesanan.menu')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.pesanan')
            ->layout('layouts.app');
    }
}
