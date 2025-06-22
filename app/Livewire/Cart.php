<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    use WithFileUploads;

    public $cart = [];
    public $total = 0;
    public $nama_pelanggan;
    public $telepon_pelanggan;
    public $alamat_pelanggan;
    public $metode_pembayaran = 'cod';
    public $showCart = false;

    public $buktiPembayaranFile;

    public $bankTransferInfo = [
        'nama_bank' => 'Bank ABC',
        'nomor_rekening' => '1234567890',
        'nama_pemilik' => 'Sederhana Coffee Shop',
    ];
    public $eWalletInfo = [
        'nama_ewallet' => 'Dana / OVO / GoPay',
        'nomor_hp_ewallet' => '081234567890',
        'nama_pemilik' => 'Sederhana Coffee Shop',
    ];

    public function mount()
    {
        $this->loadCartFromSession();
        if (Auth::check()) {
            $user = Auth::user();
            $this->nama_pelanggan = $user->name;
            $this->telepon_pelanggan = $user->phone ?? '';
            $this->alamat_pelanggan = $user->address ?? '';
        }
    }

    #[On('cartUpdated')]
    public function loadCartFromSession()
    {
        $this->cart = session('cart', []);
        $this->updateTotal();
    }

    public function removeFromCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            $removedQuantity = $this->cart[$menuId]['quantity'];
            unset($this->cart[$menuId]);
            $this->updateTotal();
            $this->saveCartToSession();
            $this->dispatch('cartUpdated');

            $menuItem = \App\Models\Menu::find($menuId); // Perbaiki namespace jika perlu
            if ($menuItem) {
                $menuItem->stock += $removedQuantity;
                $menuItem->save();
            }
        }
    }

    public function updateQuantity($menuId, $newQuantity)
    {
        $newQuantity = (int) $newQuantity;
        $oldQuantity = $this->cart[$menuId]['quantity'] ?? 0;
        $quantityChange = $newQuantity - $oldQuantity;

        $menuItem = \App\Models\Menu::find($menuId); // Perbaiki namespace jika perlu

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
        foreach ($this->cart as $item) {
            $menuItem = \App\Models\Menu::find($item['id']); // Perbaiki namespace jika perlu
            if ($menuItem) {
                $menuItem->stock += $item['quantity'];
                $menuItem->save();
            }
        }

        $this->cart = [];
        $this->total = 0;
        session()->forget('cart');
    }

    public function submitOrder()
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:255',
            'telepon_pelanggan' => 'required|string|max:20',
            'alamat_pelanggan' => 'required|string|max:500',
            'metode_pembayaran' => 'required|in:cod,transfer_bank,e_wallet',
            'cart' => 'required|array|min:1',
        ];

        if (in_array($this->metode_pembayaran, ['transfer_bank', 'e_wallet'])) {
            $rules['buktiPembayaranFile'] = 'required|image|max:2048';
        } else {
            $rules['buktiPembayaranFile'] = 'nullable|image|max:2048';
        }

        $this->validate($rules, [
            'nama_pelanggan.required' => 'Nama pelanggan wajib diisi.',
            'telepon_pelanggan.required' => 'Nomor telepon wajib diisi.',
            'alamat_pelanggan.required' => 'Alamat pelanggan wajib diisi.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in' => 'Metode pembayaran tidak valid.',
            'cart.min' => 'Keranjang belanja tidak boleh kosong. Silakan tambahkan menu terlebih dahulu.',
            'buktiPembayaranFile.required' => 'Bukti pembayaran wajib diunggah untuk metode ini.',
            'buktiPembayaranFile.image' => 'Bukti pembayaran harus berupa gambar.',
            'buktiPembayaranFile.max' => 'Ukuran bukti pembayaran tidak boleh lebih dari 2MB.',
        ]);

        try {
            $buktiPembayaranPath = null;
            if ($this->buktiPembayaranFile) {
                $buktiPembayaranPath = $this->buktiPembayaranFile->store('bukti_pembayaran', 'public');
            }

            $pesanan = Pesanan::create([
                'user_id' => Auth::id(),
                'nama_pelanggan' => $this->nama_pelanggan,
                'telepon_pelanggan' => $this->telepon_pelanggan,
                'alamat_pelanggan' => $this->alamat_pelanggan,
                'metode_pembayaran' => $this->metode_pembayaran,
                'total_harga' => $this->total,
                'status' => 'menunggu',
                'bukti_pembayaran' => $buktiPembayaranPath,
            ]);

            foreach ($this->cart as $item) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['id'],
                    'kuantitas' => $item['quantity'],
                    'harga' => $item['price'],
                ]);
            }

            $this->cart = [];
            $this->total = 0;
            $this->nama_pelanggan = '';
            $this->telepon_pelanggan = '';
            $this->alamat_pelanggan = '';
            $this->metode_pembayaran = 'cod';
            $this->buktiPembayaranFile = null;
            session()->forget('cart');
            $this->dispatch('cartCleared');

            return redirect()->to(route('order.confirmation', ['pesananId' => $pesanan->id]));

        } catch (\Exception $e) {
            \Log::error("Error placing order: " . $e->getMessage());
            Notification::make()
                ->title('Terjadi kesalahan saat membuat pesanan.')
                ->body('Detail: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
