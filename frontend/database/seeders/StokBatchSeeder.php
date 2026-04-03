<?php

/**
 * ! StokBatchSeeder - Distribusi stok_masuk dari gudang ke setiap cabang
 * * ALUR:
 * * 1. Ambil data dari tb_obat_masuk (stok gudang)
 * * 2. Distribusikan stok_masuk secara random ke setiap cabang
 * * 3. Insert tb_stok_batch per cabang (stok_keluar=0, di-update nanti oleh ObatKeluarSeeder)
 * * NOTE: Jalan SETELAH ObatMasukSeeder, SEBELUM ObatKeluarSeeder
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\StokStatusHelper;

class StokBatchSeeder extends Seeder
{
    public function run(): void
    {
        // ! Hapus data lama
        DB::table('tb_stok_batch')->delete();

        // ! Ambil semua data obat_masuk dengan safety_stock dari tb_obat
        $obatMasuks = DB::table('tb_obat_masuk')
            ->join('tb_obat', 'tb_obat_masuk.id_obat', '=', 'tb_obat.id_obat')
            ->select(
                'tb_obat_masuk.id_obat_masuk',
                'tb_obat_masuk.id_obat',
                'tb_obat_masuk.kode_batch',
                'tb_obat_masuk.tanggal_kedaluwarsa',
                'tb_obat_masuk.stok_masuk',
                'tb_obat.safety_stock'
            )
            ->get();

        // ! Ambil semua id cabang
        $id_cabangs = DB::table('tb_cabang')->pluck('id_cabang')->toArray();
        $jumlah_cabang = count($id_cabangs);

        $stok_batches = [];

        // ! Loop setiap obat_masuk dan distribusikan stok_masuk ke setiap cabang
        foreach ($obatMasuks as $obatMasuk) {

            // ! Distribusi stok_masuk secara random ke cabang
            $distribusi = $this->distribusiStokRandom(
                $obatMasuk->stok_masuk,
                $jumlah_cabang
            );

            foreach ($id_cabangs as $index => $id_cabang) {

                $stok_masuk_cabang = $distribusi[$index];

                // ! Awalnya stok_keluar = 0, akan di-update oleh ObatKeluarSeeder
                $status_stok = StokStatusHelper::getStatus(
                    $stok_masuk_cabang,
                    $obatMasuk->safety_stock
                );

                $stok_batches[] = [
                    'id_obat' => $obatMasuk->id_obat,
                    'id_cabang' => $id_cabang,
                    'kode_batch' => $obatMasuk->kode_batch,
                    'tanggal_kedaluwarsa' => $obatMasuk->tanggal_kedaluwarsa,
                    'stok_masuk' => $stok_masuk_cabang,
                    'stok_keluar' => 0,
                    'stok_sisa' => $stok_masuk_cabang,
                    'status_stok' => $status_stok,
                    'tujuan_penyesuaian' => 'Tidak ada penyesuaian',
                    'catatan' => 'Tidak ada catatan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // ! Insert per 500 baris
        foreach (array_chunk($stok_batches, 500) as $chunk) {
            DB::table('tb_stok_batch')->insert($chunk);
        }
    }

    /**
     * ! Distribusi stok secara random ke masing-masing cabang
     */
    private function distribusiStokRandom(int $total_stok, int $jumlah_cabang): array
    {
        if ($jumlah_cabang <= 0)
            return [];
        if ($jumlah_cabang === 1)
            return [$total_stok];

        $distribusi = [];
        $remaining = $total_stok;

        for ($i = 0; $i < $jumlah_cabang - 1; $i++) {
            $max_untuk_cabang_ini = max(1, $remaining - ($jumlah_cabang - $i - 1));
            $qty = rand(1, $max_untuk_cabang_ini);
            $distribusi[] = $qty;
            $remaining -= $qty;
        }

        // ! Cabang terakhir mendapat sisa
        $distribusi[] = $remaining;

        return $distribusi;
    }
}
