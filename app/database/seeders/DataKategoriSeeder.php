<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // insertOrIgnore = skip jika sudah ada, tidak error
        DB::table('tb_kategori')->insertOrIgnore([
            ['nama_kategori' => 'Analgesik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antipiretik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antibiotik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antihipertensi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antidiabetik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiinflamasi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antihistamin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antitusif & Ekspektoran', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antasida', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antidiabetik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Hipertensi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Kolesterol', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Vitamin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Diuretik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antikoagulan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiemetik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antijamur', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiviral', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Kortikosteroid', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Bronkodilator', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antasida', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Laksatif', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiparasit', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Imunomodulator', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antipsikotik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antidepresan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Anestesi Lokal', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antikonvulsan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiparkinson', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiglaukoma', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antitusif & Ekspektoran', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Antiplatelet', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Hormonal', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Imunosupresan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Hormon Pertumbuhan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Hormon Seks', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }
}
