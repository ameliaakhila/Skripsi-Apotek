<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // insertOrIgnore = skip jika sudah ada, tidak error
        DB::table('tb_supplier')->insertOrIgnore([
            [
                'nama_supplier' => 'PT Kalbe Farma Tbk',
                'alamat_supplier' => 'Jl. Letjen Suprapto No.4, Jakarta',
                'no_telp' => '021-12345678',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Dexa Medica',
                'alamat_supplier' => 'Jl. Raya Bogor Km.26, Jakarta',
                'no_telp' => '021-87654321',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Kimia Farma',
                'alamat_supplier' => 'Jl. Veteran No.9, Bandung',
                'no_telp' => '022-33445566',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Novel',
                'alamat_supplier' => 'Jl. Diponegoro No.45, Surabaya',
                'no_telp' => '031-55667788',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Soho Global Health',
                'alamat_supplier' => 'Jl. Sultan Agung No.12, Yogyakarta',
                'no_telp' => '0274-99887766',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT. Molex Ayus Pharmaceutical',
                'alamat_supplier' => 'Jl. Sultan Agung No.12,  Tangerang, Banten.',
                'no_telp' => '021-5960312',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Sido Muncul',
                'alamat_supplier' => 'Jl. Raya Semarang No.78, Semarang',
                'no_telp' => '024-22334455',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Hexapharm Jaya',
                'alamat_supplier' => 'Jl. MH Thamrin No.10, Jakarta',
                'no_telp' => '021-44556677',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Kalbe Nutritionals',
                'alamat_supplier' => 'Jl. TB Simatupang No.22, Jakarta',
                'no_telp' => '021-66778899',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Combiphar',
                'alamat_supplier' => 'Jl. Gatot Subroto No.55, Jakarta',
                'no_telp' => '021-77889900',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_supplier' => 'PT Sanbe',
                'alamat_supplier' => 'Jl. Jend. Sudirman No.33, Jakarta',
                'no_telp' => '021-88990011',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
