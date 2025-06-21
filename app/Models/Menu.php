<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus'; // Pastikan nama tabel ini benar

    protected $fillable = [
        'kategori_menu_id',
        'nama', // Ubah dari 'nama_menu' menjadi 'nama'
        'deskripsi', // Ubah dari 'deskripsi_menu' menjadi 'deskripsi'
        'harga', // Ini sudah benar
        'gambar', // Ubah dari 'foto_menu' menjadi 'gambar'
    ];

    public function kategoriMenu()
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_menu_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriMenu::class, 'kategori_menu_id');
    }
}
