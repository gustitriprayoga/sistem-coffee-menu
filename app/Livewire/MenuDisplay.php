<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Livewire\Attributes\On;
// PASTIKAN BARIS INI ADA UNTUK FILAMENT NOTIFICATIONS
use Filament\Notifications\Notification;

class MenuDisplay extends Component
{
    public $kategoriMenus;
    public $cart = [];
    public $total = 0;

    public function mount()
    {
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
                'name' => $menuName,
                'price' => $menuPrice,
                'quantity' => 1
            ];
        }
        $this->updateTotal();
        $this->saveCartToSession();
        $this->dispatch('cartUpdated');

        // TAMBAHKAN KODE NOTIFIKASI INI
        Notification::make()
            ->title("'$menuName' berhasil ditambahkan ke keranjang!")
            ->success()
            ->duration(3000) // Notifikasi akan hilang setelah 3 detik
            ->send();
    }

    public function removeFromCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            unset($this->cart[$menuId]);
            $this->updateTotal();
            $this->saveCartToSession();
            $this->dispatch('cartUpdated');
            // Opsional: notifikasi ketika item dihapus
            Notification::make()
                ->title('Item berhasil dihapus dari keranjang.')
                ->warning()
                ->duration(3000)
                ->send();
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
        // Opsional: notifikasi ketika kuantitas diupdate
        Notification::make()
            ->title('Kuantitas item diperbarui.')
            ->info()
            ->duration(2000)
            ->send();
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
        // Anda juga bisa menambahkan notifikasi di sini setelah pesanan berhasil (jika perlu)
    }

    public function render()
    {
        return view('livewire.menu-display');
    }
}
