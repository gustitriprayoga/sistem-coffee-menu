<?php

namespace Database\Seeders;

use App\Models\KategoriMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriMenu::create([
            'nama' => 'Makanan',
        ]);
        KategoriMenu::create([
            'nama' => 'Minuman',
        ]);

    }
}
