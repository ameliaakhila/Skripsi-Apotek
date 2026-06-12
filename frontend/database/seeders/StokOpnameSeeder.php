<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supaya tidak dobel saat seed ulang
        DB::table('tb_stok_opname')->delete();

        $obats = DB::table('tb_obat')->pluck('id_obat');
        $cabangs = DB::table('tb_cabang')->pluck('id_cabang');
        $stok_batches = DB::table('tb_stok_batch')->pluck('id_stok_batch');
        $users = DB::table('users')->pluck('id_user');
        $users = $users->random();

        // buat keterangan stok opname 'Menipis', 'lebih', 'sesuai'
        $keterangan = [
            'Menipis',
            'Lebih',
            'Sesuai',
        ];

        $stok_opnames = [];
        $counter = 1;

        foreach ($obats as $obat) {
            foreach ($cabangs as $cabang_id) {
                $stok_opnames[] = [
                    // Format kode opname: OP0001, OP0002, dst (6 karakter max)
                    'kode_opname' => 'OP' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'tanggal_opname' => now()->subDays(rand(0, 30)),
                    'stok_sistem' => rand(0, 500),
                    'stok_fisik' => rand(0, 500),
                    'selisih' => rand(-50, 50),
                    'keterangan' => $keterangan[array_rand($keterangan)],
                    'id_stok_batch' => $stok_batches->random(),
                    'id_user' => $users,
                    'id_obat' => $obat,
                    'id_cabang' => $cabang_id,
                ];
                $counter++;
            }
        }

        foreach (array_chunk($stok_opnames, 500) as $chunk) {
            DB::table('tb_stok_opname')->insert($chunk);
        }
    }
}
