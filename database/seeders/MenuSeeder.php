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
            'nama' => 'Indomie Rebus',
            'harga' => 10000,
            'deskripsi' => 'Indomie Rebus adalah mie instan yang direbus dengan bumbu khas Indonesia.',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Indomie Rebus Goreng',
            'harga' => 10000,
            'deskripsi' => 'Indomie Rebus Goreng adalah mie instan yang digoreng dengan bumbu khas Indonesia.',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Pop Mie',
            'harga' => 8000,
            'deskripsi' => 'Pop Mie adalah mie instan yang siap disajikan dengan bumbu lezat.',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Pisang Krispy Lumer',
            'harga' => 10000,
            'deskripsi' => 'Pisang Krispy Lumer adalah pisang yang digoreng dengan tepung krispy yang lezat.',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Sosis/Nugget',
            'harga' => 5000,
            'deskripsi' => 'Sosis atau Nugget adalah makanan ringan yang digoreng dengan tepung krispy.',
            'kategori_menu_id' => 1,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Soda Hohe',
            'harga' => 8000,
            'deskripsi' => 'Soda Hohe adalah minuman soda yang menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'extrajoss/extrajoss susu',
            'harga' => 5000,
            'deskripsi' => 'Extrajoss adalah minuman energi yang menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Kukubima/Kukubima Susu',
            'harga' => 5000,
            'deskripsi' => 'Kukubima adalah minuman energi yang menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Gingseng',
            'harga' => 7000,
            'deskripsi' => 'Gingseng adalah minuman herbal yang menyehatkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Susu',
            'harga' => 5000,
            'deskripsi' => 'Susu adalah minuman sehat yang kaya akan kalsium.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Teh',
            'harga' => 5000,
            'deskripsi' => 'Teh Manis adalah minuman teh yang manis dan menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Chocolatos',
            'harga' => 7000,
            'deskripsi' => 'Chocolatos adalah minuman cokelat yang lezat dan menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Beng Beng',
            'harga' => 5000,
            'deskripsi' => 'Beng Beng adalah minuman cokelat yang lezat dan menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Hilo',
            'harga' => 7000,
            'deskripsi' => 'Hilo adalah minuman susu yang kaya akan nutrisi.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Top Cappuccino',
            'harga' => 5000,
            'deskripsi' => 'Top Cappuccino adalah minuman kopi cappuccino yang lezat.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Top Gula Aren',
            'harga' => 7000,
            'deskripsi' => 'Top Gula Aren adalah minuman kopi dengan gula aren yang lezat.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Luwak White Coffee',
            'harga' => 5000,
            'deskripsi' => 'Luwak White Coffee adalah minuman kopi yang terbuat dari biji kopi luwak yang berkualitas tinggi.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Nutrisari / Nutrisari Susu',
            'harga' => 5000,
            'deskripsi' => 'Nutrisari adalah minuman serbuk yang kaya akan vitamin dan nutrisi.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Nescafe',
            'harga' => 5000,
            'deskripsi' => 'Nescafe adalah minuman kopi instan yang lezat dan menyegarkan.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);

        Menu::create([
            'nama' => 'Kopi Kapal Api',
            'harga' => 5000,
            'deskripsi' => 'Kopi Kapal Api adalah minuman kopi yang terbuat dari biji kopi berkualitas tinggi.',
            'kategori_menu_id' => 2,
            'stock' => 100, // Menambahkan stok
            'gambar' => 'nasi_goreng.jpg',
        ]);
    }
}
