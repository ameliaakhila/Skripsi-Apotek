<?php

/**
 * ! ObatMasukSeeder - Generate dummy data untuk tb_obat_masuk
 * * Gunakan: php artisan db:seed --class=ObatMasukSeeder
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatMasukSeeder extends Seeder
{
    public function run(): void
    {
        // ? Hapus data lama (prevent duplicate)
        DB::table('tb_obat_masuk')->delete();

        // ? Ambil semua data obat
        $obats = DB::table('tb_obat')->get();

        // ? Tentukan jumlah batch per obat (bisa diubah sesuai kebutuhan)
        $batchPerObat = 3;

        // ? Array untuk menampung data yang akan diinsert
        $data_obat_masuk = [];

        // ? Database transaction (rollback otomatis jika error)
        DB::transaction(function () use ($obats, $batchPerObat, &$data_obat_masuk) {

            $batchCounter = 0; // Counter untuk unique kode batch

            foreach ($obats as $obat) {

                // ? Buat multiple batch untuk setiap obat
                for ($i = 0; $i < $batchPerObat; $i++) {

                    $batchCounter++;

                    // ? Random stok masuk: 100-500 unit
                    $stokMasuk = rand(100, 500);


                    // ? Tanggal masuk = hari ini - i hari (supaya setiap batch berbeda hari)
                    $tanggal_masuk = now()->subDays($i);

                    // ? Format kode batch: BAC + tanggal + SF + nomor urut 3 digit
                    // ? Contoh: BAC050326SF001, BAC050326SF002, dst
                    $kodeBatch = 'BAC' . $tanggal_masuk->format('dmy') . 'SF' . str_pad($batchCounter, 3, '0', STR_PAD_LEFT);

                    // ? Build array untuk 1 record obat masuk
                    $data_obat_masuk[] = [
                        'kode_batch' => $kodeBatch,
                        'tanggal_masuk' => $tanggal_masuk,
                        'tanggal_kedaluwarsa' => $tanggal_masuk->copy()->addMonths(rand(6, 12))->addYears(rand(1, 2)),
                        'stok_masuk' => 100,
                        'id_obat' => $obat->id_obat,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        });

        // ? Insert per 500 baris (lebih efisien dari 1-1)
        foreach (array_chunk($data_obat_masuk, 500) as $chunk) {
            DB::table('tb_obat_masuk')->insert($chunk);
        }
    }
}
