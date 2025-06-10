<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SpatieRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Cafe',
            'email' => 'admin@cafe.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole(Role::firstOrCreate(['name' => 'admin']));

        $user = User::create([
            'name' => 'Pelanggan Biasa',
            'email' => 'user@cafe.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole(Role::firstOrCreate(['name' => 'pelanggan']));
    }
}
