<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $fillable = [
        'user_id', // Tambahkan jika ada user_id yang bisa diisi
        'nama_pelanggan',
        'telepon_pelanggan',
        'alamat_pelanggan',
        'metode_pembayaran',
        'bukti_pembayaran',
        'total_harga', // TAMBAHKAN INI
        'status',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id'); // Pastikan 'pesanan_id' adalah nama kolom foreign key di detail_pesanans
    }
}
