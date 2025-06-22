<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'nama' => 'Nasi Goreng',
            'harga' => 15000,
            'deskripsi' => 'Nasi goreng spesial dengan telur dan ayam',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Ayam Penyet',
            'harga' => 20000,
            'deskripsi' => 'Ayam goreng dengan sambal khas',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'ayam_penyet.jpg',
        ]);

        Menu::create([
            'nama' => 'Es Teh Manis',
            'harga' => 5000,
            'deskripsi' => 'Minuman teh manis segar',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'es_teh_manis.jpg',
        ]);

        Menu::create([
            'nama' => 'Kopi Susu',
            'harga' => 10000,
            'deskripsi' => 'Kopi dengan susu kental manis',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'kopi_susu.jpg',
        ]);

        Menu::create([
            'nama' => 'Keripik Singkong',
            'harga' => 8000,
            'deskripsi' => 'Keripik singkong renyah',
            'kategori_menu_id' => 3,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'keripik_singkong.jpg',
        ]);

        Menu::create([
            'nama' => 'Pisang Goreng',
            'harga' => 7000,
            'deskripsi' => 'Pisang goreng manis dan renyah',
            'kategori_menu_id' => 3,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'pisang_goreng.jpg',
        ]);
    }
}
