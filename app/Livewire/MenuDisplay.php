<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Livewire\Attributes\On;
use Filament\Notifications\Notification; // Pastikan baris ini ada

class MenuDisplay extends Component
{
    public $kategoriMenus;
    public $cart = [];
    public $total = 0;
    public $selectedCategoryId = null; // Tambahkan properti untuk ID kategori yang dipilih

    public function mount()
    {
        $this->loadKategoriMenus();
        $this->loadCartFromSession();
    }

    // Metode untuk memuat kategori menu berdasarkan filter
    #[On('filterByCategory')]
    public function filterByCategory($categoryId = null)
    {
        $this->selectedCategoryId = $categoryId;
        $this->loadKategoriMenus();
    }

    protected function loadKategoriMenus()
    {
        if ($this->selectedCategoryId) {
            $this->kategoriMenus = KategoriMenu::where('id', $this->selectedCategoryId)
                                    ->with(['menus' => function($query) {
                                        $query->orderBy('nama', 'asc'); // Order menus by name
                                    }])
                                    ->get();
        } else {
            $this->kategoriMenus = KategoriMenu::with(['menus' => function($query) {
                                        $query->orderBy('nama', 'asc'); // Order menus by name
                                    }])
                                    ->get();
        }
    }


    public function addToCart($menuId, $menuName, $menuPrice)
    {
        $menu = Menu::find($menuId); // Dapatkan model Menu untuk memeriksa stok

        if (!$menu) {
            Notification::make()
                ->title('Menu tidak ditemukan!')
                ->danger()
                ->duration(3000)
                ->send();
            return;
        }

        if (isset($this->cart[$menuId])) {
            // Jika item sudah ada di keranjang, cek stok untuk penambahan kuantitas
            if ($menu->stock > $this->cart[$menuId]['quantity']) {
                $this->cart[$menuId]['quantity']++;
                Notification::make()
                    ->title("'$menuName' berhasil ditambahkan ke keranjang!")
                    ->success()
                    ->duration(3000)
                    ->send();
            } else {
                Notification::make()
                    ->title("Stok '$menuName' tidak cukup!")
                    ->danger()
                    ->duration(3000)
                    ->send();
            }
        } else {
            // Jika item belum ada di keranjang, cek stok minimal 1
            if ($menu->stock > 0) {
                $this->cart[$menuId] = [
                    'id' => $menuId,
                    'name' => $menuName,
                    'price' => $menuPrice,
                    'quantity' => 1
                ];
                Notification::make()
                    ->title("'$menuName' berhasil ditambahkan ke keranjang!")
                    ->success()
                    ->duration(3000)
                    ->send();
            } else {
                Notification::make()
                    ->title("Stok '$menuName' habis!")
                    ->danger()
                    ->duration(3000)
                    ->send();
            }
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
        $menu = Menu::find($menuId);

        if (!$menu) {
            Notification::make()
                ->title('Menu tidak ditemukan!')
                ->danger()
                ->duration(3000)
                ->send();
            return;
        }

        if (isset($this->cart[$menuId]) && $quantity > 0) {
            // Hanya izinkan update jika kuantitas baru tidak melebihi stok
            if ($quantity <= $menu->stock) {
                $this->cart[$menuId]['quantity'] = $quantity;
                Notification::make()
                    ->title('Kuantitas item diperbarui.')
                    ->info()
                    ->duration(2000)
                    ->send();
            } else {
                Notification::make()
                    ->title("Stok '$menu->nama' hanya tersedia " . $menu->stock . " unit.")
                    ->danger()
                    ->duration(3000)
                    ->send();
                // Kembalikan kuantitas ke nilai maksimum yang tersedia jika melebihi stok
                $this->cart[$menuId]['quantity'] = $menu->stock;
            }
        } elseif ($quantity <= 0) {
            unset($this->cart[$menuId]);
            Notification::make()
                ->title('Item dihapus karena kuantitas nol.')
                ->warning()
                ->duration(2000)
                ->send();
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
        // PERHATIAN: Implementasi ini hanya mengosongkan keranjang di sesi.
        // Untuk sistem stok yang sesungguhnya, Anda perlu mengurangi stok di database
        // saat pesanan dikonfirmasi dan mengembalikan stok saat pesanan dibatalkan.
        // Implementasi ini TIDAK mengurangi stok dari database.

        $this->cart = [];
        $this->total = 0;
        session()->forget('cart');
        Notification::make()
            ->title('Keranjang berhasil dikosongkan.')
            ->info()
            ->duration(3000)
            ->send();
    }

    public function render()
    {
        return view('livewire.menu-display');
    }
}
