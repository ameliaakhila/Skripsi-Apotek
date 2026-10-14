<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // insertOrIgnore = skip jika kode_cabang sudah ada (tidak error duplicate)
        DB::table('tb_cabang')->insertOrIgnore([
            [
                'kode_cabang' => 'SF01',
                'nama_cabang' => 'Apotek Sena Farma - Cijeruk',
                'tipe' => 'pusat',
                'alamat_cabang' => 'Jl. Raya Cijeruk No.10, Cijeruk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_cabang' => 'SF02',
                'nama_cabang' => 'Apotek Sena Farma - Gorda',
                'tipe' => 'cabang',
                'alamat_cabang' => 'Jl. Gorda No.5, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_cabang' => 'SF03',
                'nama_cabang' => 'Apotek Sena Farma - Ciujung',
                'tipe' => 'cabang',
                'alamat_cabang' => 'Jl. Tajur No.22, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_cabang' => 'SF04',
                'nama_cabang' => 'Apotek Alam Farma - Lipatik',
                'tipe' => 'cabang',
                'alamat_cabang' => 'Jl. Dramaga No.15, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_cabang' => 'SF05',
                'nama_cabang' => 'Apotek Gayatri Farma - Pipitan',
                'tipe' => 'cabang',
                'alamat_cabang' => 'Jl. Sukasari No.8, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
