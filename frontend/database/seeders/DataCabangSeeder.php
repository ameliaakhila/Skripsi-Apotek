<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ! Data Dummy Cabang
        DB::table('tb_cabang')->insert([
            [
                'nama_cabang' => 'Apotek Sena Farma - Cijeruk',
                'kode_cabang' => 'ASF001',
                'alamat_cabang' => 'Jl. Raya Cijeruk No.10, Cijeruk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_cabang' => 'Apotek Sena Farma - Gorda',
                'kode_cabang' => 'ASF002',
                'alamat_cabang' => 'Jl. Gorda No.5, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_cabang' => 'Apotek Sena Farma - Ciujung',
                'kode_cabang' => 'ASF003',
                'alamat_cabang' => 'Jl. Tajur No.22, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_cabang' => 'Apotek Alam Farma - Lipatik',
                'kode_cabang' => 'ASF004',
                'alamat_cabang' => 'Jl. Dramaga No.15, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_cabang' => 'Apotek Gayatri Farma - Pipitan',
                'kode_cabang' => 'ASF005',
                'alamat_cabang' => 'Jl. Sukasari No.8, Bogor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
