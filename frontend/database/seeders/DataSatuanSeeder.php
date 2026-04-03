<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ! Data Dummy Satuan
        DB::table('tb_satuan')->insert([
            ['nama_satuan' => 'Strip', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Botol', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Box', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Tube', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Pcs', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
