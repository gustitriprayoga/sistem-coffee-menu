<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu; // Hanya model Menu yang perlu di sini, KategoriMenu dimuat di WelcomePage
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class MenuDisplay extends Component
{
    // Properti filter yang DITERIMA DARI WelcomePage sebagai parameter mount
    public $search = '';
    public $selectedCategory = null;

    public $cart = [];
    public $total = 0;

    // Properti untuk modal detail produk
    public $showProductModal = false;
    public $selectedMenu = null;
    public $modalQuantity = 1;

    // Metode mount menerima parameter dari komponen induk (WelcomePage)
    public function mount($search = '', $selectedCategory = null)
    {
        $this->search = $search;
        $this->selectedCategory = $selectedCategory;
        // Kategori menu tidak perlu dimuat di sini, karena sudah dimuat WelcomePage
        $this->loadCartFromSession();
    }

    // Computed property untuk mendapatkan menu yang sudah difilter
    public function getFilteredMenusProperty()
    {
        $query = Menu::query();

        // Filter berdasarkan kategori
        if ($this->selectedCategory !== 'all' && !is_null($this->selectedCategory)) {
            $query->where('kategori_menu_id', $this->selectedCategory);
        }

        // Filter berdasarkan query pencarian
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            });
        }

        // Filter: hanya tampilkan jika stok > 0 (asumsi kolom 'stock' sudah ada di DB)
        $query->where('stock', '>', 0);

        return $query->get();
    }

    // --- METODE UNTUK MODAL DETAIL PRODUK ---
    public function openProductModal($menuId)
    {
        $this->selectedMenu = Menu::find($menuId);
        if ($this->selectedMenu) {
            $this->modalQuantity = 1;
            $this->showProductModal = true;
        } else {
            Notification::make()->title('Produk tidak ditemukan.')->danger()->send();
        }
    }

    public function closeProductModal() // <-- METODE INI SEKARANG ADA
    {
        $this->showProductModal = false;
        $this->selectedMenu = null;
        $this->modalQuantity = 1;
    }

    public function incrementQuantity()
    {
        if ($this->selectedMenu && $this->modalQuantity < $this->selectedMenu->stock) {
            $this->modalQuantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->modalQuantity > 1) {
            $this->modalQuantity--;
        }
    }
    // --- AKHIR METODE MODAL ---


    public function addToCart($menuId, $menuName, $menuPrice, $quantity = 1)
    {
        $menuItem = Menu::find($menuId); // Dapatkan item menu dari DB
        if ($menuItem && $menuItem->stock >= $quantity) {
            if (isset($this->cart[$menuId])) {
                $this->cart[$menuId]['quantity'] += $quantity;
            } else {
                $this->cart[$menuId] = [
                    'id' => $menuId,
                    'name' => $menuName,
                    'price' => $menuPrice,
                    'quantity' => $quantity
                ];
            }
            $this->updateTotal();
            $this->saveCartToSession();
            $this->dispatch('cartUpdated'); // Beritahu Cart component

            // Kurangi stok di database
            $menuItem->stock -= $quantity;
            $menuItem->save();

            Notification::make()
                ->title("'$menuName' berhasil ditambahkan ke keranjang!")
                ->success()
                ->duration(3000)
                ->send();

            $this->closeProductModal(); // Tutup modal jika dipanggil dari modal
        } else {
            Notification::make()
                ->title("Stok '$menuName' tidak mencukupi atau tidak tersedia.")
                ->danger()
                ->send();
        }
    }

    public function removeFromCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            $removedQuantity = $this->cart[$menuId]['quantity'];
            unset($this->cart[$menuId]);
            $this->updateTotal();
            $this->saveCartToSession();
            $this->dispatch('cartUpdated');

            $menuItem = Menu::find($menuId);
            if ($menuItem) {
                $menuItem->stock += $removedQuantity;
                $menuItem->save();
            }

            Notification::make()
                ->title('Item berhasil dihapus dari keranjang.')
                ->warning()
                ->duration(3000)
                ->send();
        }
    }

    public function updateQuantity($menuId, $newQuantity)
    {
        $newQuantity = (int) $newQuantity;
        $oldQuantity = $this->cart[$menuId]['quantity'] ?? 0;
        $quantityChange = $newQuantity - $oldQuantity;

        $menuItem = Menu::find($menuId);

        if (!$menuItem) {
            Notification::make()->title('Produk tidak ditemukan.')->danger()->send();
            return;
        }

        if ($newQuantity <= 0) {
            $this->removeFromCart($menuId);
            return;
        }

        if ($quantityChange > 0 && $menuItem->stock < $quantityChange) {
            Notification::make()
                ->title("Stok '$menuItem->nama' tidak mencukupi untuk menambah kuantitas.")
                ->danger()
                ->send();
            $this->cart[$menuId]['quantity'] = $oldQuantity;
            $this->saveCartToSession();
            $this->updateTotal();
            return;
        }

        $menuItem->stock -= $quantityChange;
        $menuItem->save();

        $this->cart[$menuId]['quantity'] = $newQuantity;
        $this->updateTotal();
        $this->saveCartToSession();
        $this->dispatch('cartUpdated');

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
        // Kembalikan stok dari item di keranjang jika pesanan dibersihkan/dibatalkan
        foreach ($this->cart as $item) {
            $menuItem = Menu::find($item['id']);
            if ($menuItem) {
                $menuItem->stock += $item['quantity'];
                $menuItem->save();
            }
        }

        $this->cart = [];
        $this->total = 0;
        session()->forget('cart');
    }

    public function render()
    {
        return view('livewire.menu-display', [
            'filteredMenus' => $this->filteredMenus,
        ]);
    }
}
