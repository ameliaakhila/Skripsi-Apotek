<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // !  Data Dummy Obat
        DB::table('tb_obat')->insert([
            [
                'nama_obat' => 'Paracetamol 500mg',
                'pabrik_obat' => 'PT Kalbe Farma Tbk',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Analgesik')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Amoxicillin 500mg',
                'pabrik_obat' => 'PT Dexa Medica',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antibiotik')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Cefixime 200mg',
                'pabrik_obat' => 'PT Novel',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antibiotik')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Vitamin C 500mg',
                'pabrik_obat' => 'PT Kimia Farma',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Vitamin')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Ibuprofen 400mg',
                'pabrik_obat' => 'PT Kalbe Farma Tbk',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Analgesik')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Cetirizine 10mg',
                'pabrik_obat' => 'PT Novel',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antihistamin')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Ranitidine 150mg',
                'pabrik_obat' => 'PT Kimia Farma Tbk',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antasida')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Metformin 500mg',
                'pabrik_obat' => 'PT Dexa Medica',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antidiabetik')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Amlodipine 5mg',
                'pabrik_obat' => 'PT Kalbe Farma Tbk',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Hipertensi')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Simvastatin 10mg',
                'pabrik_obat' => 'PT Kimia Farma',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Kolesterol')->value('id_kategori_obat')
            ],
            [
                'nama_obat' => 'Loratadine 10mg',
                'pabrik_obat' => 'PT Novel',
                'satuan_obat' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
                'id_kategori_obat' => DB::table('tb_kategori_obat')->where('nama_kategori_obat', 'Antihistamin')->value('id_kategori_obat')
            ]
        ]);
    }
}
