<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_kategori_obat')->insert([
            ['nama_kategori_obat' => 'Analgesik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antipiretik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antibiotik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antihipertensi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antidiabetik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antiinflamasi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antihistamin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antitusif & Ekspektoran', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antasida', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Antidiabetik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Hipertensi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Kolesterol', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Vitamin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori_obat' => 'Diuretik', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
