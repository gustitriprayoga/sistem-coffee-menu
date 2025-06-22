<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu; // Import model KategoriMenu
use Livewire\Attributes\Url; // Untuk sinkronisasi URL di komponen induk
use Illuminate\Support\Facades\Auth; // Jika perlu untuk navigasi header

class WelcomePage extends Component
{
    // Properti untuk filter/pencarian, disinkronkan dengan URL
    #[Url]
    public $search = '';
    #[Url]
    public $selectedCategory = 'all'; // Default ke 'all'

    public $kategoriMenus; // Kategori menu akan dimuat di sini

    public function mount()
    {
        $this->kategoriMenus = KategoriMenu::all(); // Muat semua kategori
        // Set kategori default ke 'all' jika URL category invalid
        if (!$this->kategoriMenus->pluck('id')->contains($this->selectedCategory) && $this->selectedCategory !== 'all') {
            $this->selectedCategory = 'all';
        }
    }

    // Metode untuk menerapkan filter kategori
    public function selectCategory($categoryId)
    {
        $this->selectedCategory = ($categoryId === 'all') ? 'all' : (int) $categoryId;
    }

    // Metode untuk memperbarui filter pencarian
    public function updatedSearch()
    {
        // Livewire secara otomatis akan memperbarui komponen anak
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
