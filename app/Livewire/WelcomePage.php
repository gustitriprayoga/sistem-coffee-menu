<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriMenu;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class WelcomePage extends Component
{
    #[Url]
    public $search = '';
    #[Url]
    public $selectedCategory = 'all';

    public $kategoriMenus;

    public function mount()
    {
        $this->kategoriMenus = KategoriMenu::all();
        if (!$this->kategoriMenus->pluck('id')->contains($this->selectedCategory) && $this->selectedCategory !== 'all') {
            $this->selectedCategory = 'all';
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = ($categoryId === 'all') ? 'all' : (int) $categoryId;
    }

    public function updatedSearch()
    {
        // Livewire akan otomatis memperbarui komponen anak
    }

    public function render()
    {
        return view('livewire.welcome-page');
    }
}
