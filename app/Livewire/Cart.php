<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
// IMPORT TRAIT INI UNTUK UPLOAD FILE
use Livewire\WithFileUploads;

class Cart extends Component
{
    // GUNAKAN TRAIT INI
    use WithFileUploads;

    public $cart = [];
    public $total = 0;
    public $nama_pelanggan;
    public $telepon_pelanggan;
    public $alamat_pelanggan;
    public $metode_pembayaran = 'cod';
    public $showCart = false;

    // Properti baru untuk upload file bukti pembayaran
    public $buktiPembayaranFile; // <-- PROPERTI BARU

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

    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }

    public function submitOrder()
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:255',
            'telepon_pelanggan' => 'required|string|max:20',
            'alamat_pelanggan' => 'required|string|max:500',
            'metode_pembayaran' => 'required|in:cod,transfer_bank,e_wallet,bayar_di_tempat',
            'cart' => 'required|array|min:1',
        ];

        // --- ATURAN VALIDASI KONDISIONAL UNTUK BUKTI PEMBAYARAN ---
        if (in_array($this->metode_pembayaran, ['transfer_bank', 'e_wallet'])) {
            $rules['buktiPembayaranFile'] = 'required|image|max:2048'; // Wajib, berupa gambar, max 2MB (2048 KB)
        } else {
            $rules['buktiPembayaranFile'] = 'nullable|image|max:2048'; // Boleh kosong jika COD
        }
        // --- AKHIR ATURAN VALIDASI KONDISIONAL ---

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
            // --- LOGIKA UPLOAD DAN SIMPAN FILE ---
            if ($this->buktiPembayaranFile) {
                $buktiPembayaranPath = $this->buktiPembayaranFile->store('bukti_pembayaran', 'public');
            }
            // --- AKHIR LOGIKA UPLOAD DAN SIMPAN FILE ---

            $pesanan = Pesanan::create([
                'nama_pelanggan' => $this->nama_pelanggan,
                'telepon_pelanggan' => $this->telepon_pelanggan,
                'alamat_pelanggan' => $this->alamat_pelanggan,
                'metode_pembayaran' => $this->metode_pembayaran,
                'total_harga' => $this->total,
                'status' => 'menunggu',
                'bukti_pembayaran' => $buktiPembayaranPath, // <-- SIMPAN PATH FILE DI SINI
            ]);

            foreach ($this->cart as $item) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'menu_id' => $item['id'],
                    'kuantitas' => $item['quantity'],
                    'harga' => $item['price'],
                ]);
            }

            // Clear the cart and reset properties
            $this->cart = [];
            $this->total = 0;
            $this->nama_pelanggan = '';
            $this->telepon_pelanggan = '';
            $this->alamat_pelanggan = '';
            $this->metode_pembayaran = 'cod';
            session()->forget('cart');
            $this->dispatch('cartCleared');

            // Redirect to the order confirmation page
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
