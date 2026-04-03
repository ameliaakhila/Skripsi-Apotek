<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin'
            ]
        );

        // Create Pharmacist (Apoteker) User
        User::firstOrCreate(
            ['email' => 'apotek@gmail.com'],
            [
                'username' => 'apotek',
                'password' => Hash::make('apotek'),
                'role' => 'apoteker'
            ]
        );

        // Create Manager (Manajer) User
        User::firstOrCreate(
            ['email' => 'manajer@gmail.com'],
            [
                'username' => 'manajer',
                'password' => Hash::make('manajer'),
                'role' => 'manajer'
            ]
        );

        $this->call([
            DataSatuanSeeder::class,
            DataKategoriSeeder::class,
            DataSupplierSeeder::class,
            DataCabangSeeder::class,
            ObatSeeder::class,
            ObatMasukSeeder::class,
            StokBatchSeeder::class,
            ObatKeluarSeeder::class,
            StokOpnameSeeder::class,
            MutasiStokSeeder::class,
        ]);
    }
}
