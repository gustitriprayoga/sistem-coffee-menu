<?php

namespace App\Livewire;

use App\Models\Menu;
use Livewire\Component;

class Keranjang extends Component
{
    public $items = [];

    public function mount()
    {
        $this->items = session()->get('keranjang', []);
    }

    public function hapus($id)
    {
        $cart = session()->get('keranjang', []);
        unset($cart[$id]);
        session()->put('keranjang', $cart);
        $this->items = $cart;
    }

    public function render()
    {
        return view('livewire.keranjang')->layout('layouts.app');
    }
}
