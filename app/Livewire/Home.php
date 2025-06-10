<?php

namespace App\Livewire;

use App\Models\Menu;
use App\Models\Kategori;
use App\Models\KategoriMenu;
use Livewire\Component;

class Home extends Component
{
    public $search = '';

    public function tambahKeranjang($id)
    {
        $menu = Menu::findOrFail($id);
        $keranjang = session()->get('keranjang', []);

        if (isset($keranjang[$id])) {
            $keranjang[$id]['qty']++;
        } else {
            $keranjang[$id] = [
                'id' => $menu->id,
                'nama' => $menu->nama,
                'harga' => $menu->harga,
                'qty' => 1,
                'gambar' => $menu->gambar
            ];
        }

        session()->put('keranjang', $keranjang);

        session()->flash('success', 'Menu berhasil ditambahkan ke keranjang.');
    }

    public function render()
    {
        $menus = Menu::with('kategori')
            ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->latest()
            ->get();

        $kategoris = KategoriMenu::all();

        return view('livewire.home', compact('menus', 'kategoris'))
            ->layout('layouts.app');
    }
}
