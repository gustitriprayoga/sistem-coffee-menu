<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Livewire\Attributes\On;

class MenuDisplay extends Component
{
    public $kategoriMenus;
    public $cart = [];
    public $total = 0;

    public function mount()
    {
        // Mengambil semua kategori beserta menu-menu terkaitnya.
        $this->kategoriMenus = KategoriMenu::with('menus')->get();
        $this->loadCartFromSession();
    }

    public function addToCart($menuId, $menuName, $menuPrice)
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            $this->cart[$menuId] = [
                'id' => $menuId,
                'name' => $menuName, // Menggunakan parameter menuName (dari kolom 'nama')
                'price' => $menuPrice, // Menggunakan parameter menuPrice (dari kolom 'harga')
                'quantity' => 1
            ];
        }
        $this->updateTotal();
        $this->saveCartToSession();
        $this->dispatch('cartUpdated');
    }

    public function removeFromCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            unset($this->cart[$menuId]);
            $this->updateTotal();
            $this->saveCartToSession();
            $this->dispatch('cartUpdated');
        }
    }

    public function updateQuantity($menuId, $quantity)
    {
        $quantity = (int) $quantity;
        if (isset($this->cart[$menuId]) && $quantity > 0) {
            $this->cart[$menuId]['quantity'] = $quantity;
        } elseif ($quantity <= 0) {
            unset($this->cart[$menuId]);
        }
        $this->updateTotal();
        $this->saveCartToSession();
        $this->dispatch('cartUpdated');
    }

    public function updateTotal()
    {
        $this->total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $this->cart));
    }

    protected function saveCartToSession()
    {
        session(['cart' => $this->cart]);
    }

    protected function loadCartFromSession()
    {
        $this->cart = session('cart', []);
        $this->updateTotal();
    }

    #[On('cartCleared')]
    public function handleCartCleared()
    {
        $this->cart = [];
        $this->total = 0;
        session()->forget('cart');
    }

    public function render()
    {
        return view('livewire.menu-display');
    }
}
