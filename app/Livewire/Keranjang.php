<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use Livewire\Attributes\On;

class Keranjang extends Component
{
    public $cart = [];
    public $total = 0;
    public $isCartOpen = false; // New property to control pop-up visibility

    protected $listeners = ['cartUpdated' => 'updateCartDisplay'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['harga'] * $item['quantity'];
        }
    }

    public function updateCartDisplay()
    {
        $this->loadCart();
    }

    public function incrementQuantity($menuId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity']++;
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function decrementQuantity($menuId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$menuId]) && $cart[$menuId]['quantity'] > 1) {
            $cart[$menuId]['quantity']--;
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
        } elseif (isset($cart[$menuId]) && $cart[$menuId]['quantity'] == 1) {
            unset($cart[$menuId]); // Remove item if quantity becomes 0
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function removeFromCart($menuId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$menuId])) {
            unset($cart[$menuId]);
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cartUpdated');
        }
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->cart = [];
        $this->total = 0;
        $this->dispatch('cartUpdated');
    }

    public function toggleCart()
    {
        $this->isCartOpen = !$this->isCartOpen;
    }

    public function render()
    {
        return view('livewire.keranjang');
    }
}
