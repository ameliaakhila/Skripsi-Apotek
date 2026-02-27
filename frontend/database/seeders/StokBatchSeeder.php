<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Illuminate\Support\days;

class StokBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    // Buat Dummy dengan jumlah Serupa dengan data obat Diatas dengan stok batch dan 5 cabang yang berbeda dengan kode cabang ASF001 sampai ASF005
    public function run(): void
    {
        // Supaya tidak dobel saat seed ulang
        DB::table('tb_stok_batch')->delete();

        $obats = DB::table('tb_obat')->pluck('id_obat');
        $kode_cabangs = DB::table('tb_cabang')->pluck('id_cabang');

        $stok_batches = [];

        // Buat 3 batch per obat per cabang
        for ($i = 1; $i <= 3; $i++) {

            foreach ($obats as $obat) {
                foreach ($kode_cabangs as $kode_cabang) {

                    $jumlah_masuk = rand(200, 500);
                    $jumlah_sisa = rand(0, $jumlah_masuk);

                    // Status stok lebih realistis
                    if ($jumlah_sisa >= 300) {
                        $status_stok = "Aman";
                    } elseif ($jumlah_sisa >= 100) {
                        $status_stok = "Menipis";
                    } else {
                        $status_stok = "Habis";
                    }

                    $stok_batches[] = [
                        'id_obat' => $obat,
                        'id_cabang' => $kode_cabang,

                        // Lebih unik & rapi
                        'kode_batch' => 'BAC-' . now()->format('YmdHis') . "-$obat-$kode_cabang-$i",

                        // pakai copy supaya Carbon tidak konflik
                        'tanggal_kedaluwarsa' => now()->copy()
                            ->addYears(rand(1, 3))
                            ->setDay(rand(1, 28)),

                        'jumlah_masuk' => $jumlah_masuk,
                        'jumlah_sisa' => $jumlah_sisa,
                        'status_stok' => $status_stok,

                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert per 500 data
        foreach (array_chunk($stok_batches, 500) as $chunk) {
            DB::table('tb_stok_batch')->insert($chunk);
        }
    }
}
