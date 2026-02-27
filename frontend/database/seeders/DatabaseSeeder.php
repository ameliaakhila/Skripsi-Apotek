<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin')
        ]);

        $this->call([
            DataSatuanSeeder::class,
            DataKategoriSeeder::class,
            DataSupplierSeeder::class,
            DataCabangSeeder::class,
            ObatSeeder::class,
            ObatMasukSeeder::class,
            StokBatchSeeder::class,
            StokOpnameSeeder::class,
        ]);

    }
}
