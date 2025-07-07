<?php

namespace Database\Factories;

use App\Models\DetailPesanan;
use App\Models\Pesanan; // Import model Pesanan
use App\Models\Menu;     // Import model Menu
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailPesananFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DetailPesanan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil ID pesanan dan menu yang ada di DB
        $pesananId = Pesanan::inRandomOrder()->first()->id ?? Pesanan::factory()->create()->id; // Buat pesanan jika belum ada
        $menuItem = Menu::inRandomOrder()->first();
        if (!$menuItem) {
            // Jika tidak ada menu di DB, buat satu menu dummy (ini untuk menghindari error jika DB kosong)
            $kategori = \App\Models\KategoriMenu::inRandomOrder()->first() ?? \App\Models\KategoriMenu::factory()->create();
            $menuItem = Menu::factory()->create(['kategori_menu_id' => $kategori->id]);
        }

        $kuantitas = $this->faker->numberBetween(1, 5); // Kuantitas random 1-5
        $harga = $menuItem->harga; // Ambil harga dari menu yang dipilih

        return [
            'pesanan_id' => $pesananId,
            'menu_id' => $menuItem->id,
            'kuantitas' => $kuantitas,
            'harga' => $harga,
        ];
    }
}
