<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Kategori;
use App\Models\KategoriMenu;

class MenuList extends Component
{
    public $menus;
    public $kategoris;
    public $search = '';

    protected $listeners = ['tambahKeranjang'];

    public function mount()
    {
        $this->kategoris = KategoriMenu::all();
        $this->menus = Menu::latest()->get();
    }

    public function updatedSearch()
    {
        $this->menus = Menu::where('nama', 'like', '%' . $this->search . '%')->latest()->get();
    }

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

        $this->dispatch('keranjangDiperbarui'); // Event opsional untuk update UI lain
    }

    public function render()
    {
        return view('livewire.menu-list');
    }
}
