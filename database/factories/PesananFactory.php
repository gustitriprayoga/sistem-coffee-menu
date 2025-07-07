<?php

namespace Database\Factories;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon; // Tambahkan ini

class PesananFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pesanan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $metodePembayaran = $this->faker->randomElement(['cod', 'transfer_bank', 'e_wallet', 'bayar_di_tempat']);
        $status = $this->faker->randomElement(['menunggu', 'diproses', 'selesai', 'dibatalkan']);
        $totalHarga = $this->faker->numberBetween(15000, 200000); // Harga random antara 15rb - 200rb

        $createdAt = Carbon::now()->subDays($this->faker->numberBetween(0, 30)); // Pesanan dibuat dalam 30 hari terakhir

        return [
            'user_id' => null, // Biarkan null untuk pesanan anonim, atau $this->faker->numberBetween(1, 10) jika ada users
            'nama_pelanggan' => $this->faker->name,
            'telepon_pelanggan' => $this->faker->phoneNumber,
            'alamat_pelanggan' => $this->faker->address,
            'metode_pembayaran' => $metodePembayaran,
            'bukti_pembayaran' => ($metodePembayaran !== 'cod' && $this->faker->boolean(50)) ? 'bukti_pembayaran/dummy_bukti.jpg' : null, // 50% kemungkinan ada bukti jika bukan COD
            'total_harga' => $totalHarga,
            'status' => $status,
            'created_at' => $createdAt,
            'updated_at' => $createdAt->addMinutes($this->faker->numberBetween(0, 120)), // Update setelah beberapa menit
        ];
    }
}
