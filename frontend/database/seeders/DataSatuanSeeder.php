<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['nama_satuan' => 'Strip'],
            ['nama_satuan' => 'Botol'],
            ['nama_satuan' => 'Box'],
            ['nama_satuan' => 'Tube'],
            ['nama_satuan' => 'Pcs']
        ]);
    }
}
