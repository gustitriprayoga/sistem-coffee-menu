<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public $kategoriMenus;
    public $menus;
    public $selectedKategoriId = null;
    public $search = '';

    protected $listeners = ['menuAddedToCart' => 'render']; // Listen for events from MenuList to re-render

    public function mount()
    {
        $this->kategoriMenus = KategoriMenu::all();
        $this->loadMenus();
    }

    public function loadMenus()
    {
        $query = Menu::query();

        if ($this->selectedKategoriId) {
            $query->where('kategori_menu_id', $this->selectedKategoriId);
        }

        if ($this->search) {
            $query->where('nama_menu', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
        }

        $this->menus = $query->get();
    }

    public function filterByKategori($kategoriId = null)
    {
        $this->selectedKategoriId = $kategoriId;
        $this->loadMenus();
    }

    public function updatedSearch()
    {
        $this->loadMenus();
    }

    public function addToCart($menuId)
    {
        $menu = Menu::find($menuId);
        if ($menu) {
            $cart = session()->get('cart', []);

            if (isset($cart[$menuId])) {
                $cart[$menuId]['quantity']++;
            } else {
                $cart[$menuId] = [
                    "id" => $menu->id,
                    "nama_menu" => $menu->nama_menu,
                    "harga" => $menu->harga,
                    "quantity" => 1,
                    "gambar_menu" => $menu->gambar_menu // Pastikan ini ada di model Menu dan bisa diakses
                ];
            }
            session()->put('cart', $cart);
            $this->dispatch('cartUpdated'); // Emit event to update cart display
            $this->dispatch('menuAdded')->self(); // Dispatch event for SweetAlert
        }
    }


    public function render()
    {
        return view('livewire.home', [
            'kategoriMenus' => $this->kategoriMenus,
            'menus' => $this->menus,
        ]);
    }
}
