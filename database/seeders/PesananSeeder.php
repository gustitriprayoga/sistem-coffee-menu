<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu; // Pastikan model Menu diimport

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada setidaknya beberapa menu di database
        if (Menu::count() === 0) {
            $this->call(KategoriSeeder::class); // Buat kategori dulu
            $this->call(MenuSeeder::class);     // Lalu buat menu
        }

        // Buat 50 pesanan palsu
        Pesanan::factory()
            ->count(50)
            ->create()
            ->each(function ($pesanan) {
                // Untuk setiap pesanan, buat antara 1 hingga 5 detail pesanan
                DetailPesanan::factory()
                    ->count(rand(1, 5))
                    ->create([
                        'pesanan_id' => $pesanan->id, // Hubungkan detail dengan pesanan yang baru dibuat
                        'menu_id' => Menu::inRandomOrder()->first()->id, // Pilih menu secara acak
                    ]);
            });
    }
}
